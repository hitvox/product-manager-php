{% extends 'layout/layout.twig.php'  %}

{% block title %}Início{% endblock %}

{% block content %}
<div class="container">
  <a href="javascript:void(0)" class="btn btn-primary open-modal" data-target="modal-add-product">Adicionar Produto</a>
</div>
{% if message %}
<div class="alert alert-success">
  <span>{{ message }}</span>
  <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
</div>
{% endif %}
<div class="container-products"></div>
<div class="load-more" style="display:none">
  <a href="#" class="btn btn-secondary btn-load-more">Carregar mais</a>
</div>


<div id="modal-add-product" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Adicionar Produto</h3>
      <span class="btn-close-modal close-modal"><i class="ri-close-line"></i></span>
    </div>
    <div class="modal-body">
    <form id="form-add-product">
            <div class="form first">
                <div class="details personal">
                    <span class="title">Informações do Produto</span>
                    <div class="fields">
                        <div class="input-field">
                            <label>Categoria</label>
                            <select name="category">
                                <option value="" selected>Sem Categoria</option>
                            </select>
                        </div>
                    </div>                    
                    <div class="fields">
                        <div class="input-field">
                            <label>Título</label>
                            <input type="text" name="title" placeholder="Título do Produto" required>
                        </div>
                        <div class="input-field">
                            <label>Formato</label>
                            <select name="product_format" required>
                                <option value="simple" selected>Simples</option>
                                <option value="variation">Com Variação</option>
                            </select>
                        </div>
                        <div class="input-field product-format-simple">
                            <label>SKU</label>
                            <input type="text" name="sku" placeholder="SKU do Produto" required>
                        </div>
                        </div>                        
                        <div class="fields">
                        <div class="input-field">
                            <label>Descrição</label>
                            <textarea
                            name="description"
                            rows="5"
                            placeholder="Descrição do Produto"
                            required                            
                            ></textarea>
                        </div>
                        <div class="input-field product-format-simple">
                            <label>Preço</label>                            
                            <input type="text" name="price" placeholder="Preço" class="mask-brl" required />
                        </div>
                        <div class="input-field product-format-simple">
                            <label>Quantidade</label>                            
                            <input type="number" name="stock" placeholder="Quantidade" required />
                        </div>
                    </div>
                </div>
                <span style="display:flex; align-items:center;gap:5px;margin-top:15px"><i class="ri-information-line"></i> <small>Adicione até 5 imagens</small></span>
                <p>
                <button id="addImageButton" type="button" class="add-button"><i class="ri-image-line"></i> Adicionar Imagem</button></p>
                <div class="container-image-upload">
                  <div id="imagePreview" class="image-preview"></div>
                  <div id="imageFields" style="display:none">                    
                  </div>
                </div>
                <div class="product-format-variations">
                    <span class="title">Variações</span>
                    <div class="fields">
                        <div class="input-field">
                            <label>Cores</label>
                            <select name="var_colors[]" class="select2-tags" multiple="multiple">
                            </select>
                        </div>
                        <div class="input-field">
                            <label>Tamanhos</label>
                            <select name="var_sizes[]" class="select2-tags" multiple="multiple">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-variations product-format-variations" style="overflow-x:auto;">
                  <table class="table-variations" style="display: none;">
                    <thead>
                      <tr>
                        <th>Variação</th>
                        <th>SKU</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                    <button class="btn-form">
                        <span class="btnText">Adicionar Produto</span>
                        <i class="uil uil-navigator"></i>
                    </button>
            </div>
        </form>
    </div>
  </div>
</div>
{% endblock %}

{% block scripts %}
<script src="/assets/js/products.js"></script>

<script>
  $(document).ready(function() {
    var page = 1;
    let url_products = '/api/products?page=' + page;
    
    loadProducts(url_products, page);

    $(document).on('click', '.btn-load-more', function(e) {
      e.preventDefault();
      page++;
      let url_products = '/api/products?page=' + page;
      loadProducts(url_products, page);
    })

    $.ajax({
      url: '/api/categories',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if(response.status){
          let categories = response.data;
          categories.forEach(category => {
              // form options
              $('[name="category"]').append(`<option value="${category.id}">${category.title}</option>`);
              $('[name="category"]').trigger('change');
          })
        }
      },
      error: function(error) {        
        toastError('Ocorreu um erro ao carregar as categorias');
        waitBodyEnd();
      }
    })
  })

  function loadProducts(url_products, page) {
    waitBody();
    $.ajax({
      url: url_products,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if(response.status) {
          let image_path = '/images/products';
          response.data.items.forEach(product => {
            let image_url = product.image ? `${image_path}/${product.id}/${product.image}` : `${image_path}/image.png`;
            let product_price = product.product_format == 'simple' ? 'R$ ' + product.price : 'Produto com Variação';
            let card = `<div class="card">
                        <div class="card-header">
                          <a href="/product/${product.id}"><img src="${image_url}" alt="" /></a>
                        </div>
                        <div class="card-body">
                          <h4><a href="/product/${product.id}">${product.title}</a></h4>
                          <h4>${product_price}</h4>
                        </div>
                      </div>`;
            $('.container-products').append(card);
          })
          waitBodyEnd();
          
          if(response.data.total_pages > page) {
            $('.load-more').show();
          }else{
            $('.load-more').hide();
          }
        }else{
          $('.container-products').html(response.message);
          waitBodyEnd();
        }
      },
      error: function(error) {
        toastError('Ocorreu um erro ao carregar os produtos');
        waitBodyEnd();
      }
    })
  }
</script>
{% endblock %}