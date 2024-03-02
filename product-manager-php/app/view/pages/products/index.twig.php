{% extends 'layout/layout.twig.php'  %}

{% block title %}Produto{% endblock %}

{% block content %}
{% if message %}
<div class="alert alert-success">
  <span>{{ message }}</span>
  <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
</div>
{% endif %}

<div class="title">
    <h2 class="section--title"></h2>
    <div style="display:flex;gap:10px">
      <a href="javascript:void(0)" class="btn btn-secondary open-modal" data-target="modal-edit-product">Editar Produto</a>
      <a href="#" class="btn btn-danger remove-product"><i class="ri-delete-bin-line"></i></a>
    </div>
</div>
<p style="margin-bottom:20px;color:#666"><small class="text-category">Sem Categoria</small></p>
<style>
.container-product {
  background-color: #fff;
  padding:40px;
  border-radius:10px;
  margin-bottom: 40px;
}
</style>
<div class="container-product">
<div class="image-preview" style="margin-bottom:20px;display:none"></div>
<div class="product-simple" style="margin-bottom:20px;display:none"></div>
<div class="product-description"></div>
<div class="product-variations" style="margin-top:40px;display:none">
<hr style="border-top:1px solid #eee; margin-bottom:20px" />
  <table class="table-variations">
    <thead>
      <tr>
        <th style="text-align:center">Variação</th>
        <th style="text-align:center">SKU</th>
        <th style="text-align:center">Preço</th>
        <th style="text-align:center">Estoque</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>
</div>

<style>
  .image-modal {
    max-width: 100%;    
  }
</style>
<div id="modal-edit-product" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Editar Produto</h3>
      <span class="btn-close-modal close-modal"><i class="ri-close-line"></i></span>
    </div>
    <div class="modal-body">
    <form id="form-edit-product">
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
                            <input type="hidden" name="product_format" value="" required>
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
                  <div class="image-preview"></div>                  
                  <div id="imageFields" style="display:none">                    
                  </div>
                </div>
                <input type="hidden" name="images_prev_removed">
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
                        <span class="btnText">Salvar Alterações</span>
                        <i class="uil uil-navigator"></i>
                    </button>
            </div>
        </form>
    </div>
  </div>
</div>
<div id="modal-photo" class="modal">
  <div class="modal-content modal-close-bg">
    <div class="modal-close" style="display:flex;justify-content:flex-end">
      <span class="btn-close-modal close-modal"><i class="ri-close-line"></i></span>
    </div>
    <div class="modal-body">
      <div style="display:flex;justify-content:center">
          <img class="image-modal" src="" />
      </div>
    </div>
  </div>
</div>
{% endblock %}

{% block scripts %}
<script>
  $(document).ready(function() {
    waitBody();

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
      }
    })

    $.ajax({
    url: '/api/products/{{ id }}',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      if(data.status) {
        $('.section--title').html(data.data.info.title);
        $('#modal-edit-product [name="title"]').val(data.data.info.title);
        $('.product-description').html(`<strong>Descrição:</strong><br />${data.data.info.description}`);
        $('#modal-edit-product [name="description"]').val(data.data.info.description);
        $('#modal-edit-product [name="product_format"]').val(data.data.info.product_format);
        if(data.data.info.category_id) {
          $('#modal-edit-product [name="category"]').val(data.data.info.category_id);
          $('#modal-edit-product [name="category"]').trigger('change');
          $('.text-category').html(data.data.info.category);
        }
        if(data.data.images.length > 0) {
          let imagePreview = document.querySelector('.container-product .image-preview');
          let imagePreviewForm = document.querySelector('#modal-edit-product .image-preview');
          data.data.images.forEach(image => {            
            let img_url = `/images/products/${data.data.info.id}/${image.image}`;
            imagePreview.innerHTML += `<div class="card-upload-img"><a href="#" data-img="${img_url}" class="open-modal" data-target="modal-photo"><img src="${img_url}" /></a></div>`;
            imagePreviewForm.innerHTML += `<div class="card-upload-img" data-img-prev="${image.id}">
                                              <img src="${img_url}" /> <a href="#" class="image-delete-prev" data-id="${image.id}"><i class="ri-close-line"></i></a>
                                            </div>`;
          })
          $('.container-product .image-preview').show();
          $('.container-product .image-preview').show();
        }
        if(data.data.info.product_format == 'variation') {
          $('.product-format-simple').hide();
          $('.product-format-variations').show();          
          $(".table-variations").css("display", "block");
          data.data.info.variations_colors.forEach(color => {
            $('[name="var_colors[]"]').append(`<option value="${color}" selected>${color}</option>`);
          })
          data.data.info.variations_sizes.forEach(size => {
            $('[name="var_sizes[]"]').append(`<option value="${size}" selected>${size}</option>`);
          })
          let table_row = '';
          data.data.variations.forEach(variation => {            
            $("[name=sku]").removeAttr("required");
            $("[name=price]").removeAttr("required");
            $("[name=stock]").removeAttr("required");
            $('.container-product .table-variations tbody').append(`<tr style="text-align:center"><td>${variation.color}</td><td>${variation.sku}</td><td>R$ ${variation.price}</td><td>${variation.stock}</td></tr>`);
            let variation_row = variation.color ? variation.color : '';
            variation_row += variation.size && variation_row ? ` - ${variation.size}` : variation.size;
            table_row += `<tr class="input-field"><td>${variation_row}<input type="hidden" name="var_color[]" value="${variation.color}" /><input type="hidden" name="var_size[]" value="${variation.size}" /></td>
            <td><input type="text" name="var_sku[]" value="${variation.sku}" /></td>
            <td><input type="text" class="mask-brl" name="var_price[]" value="${variation.price}" class="mask-brl" /></td>
            <td><input type="number" name="var_stock[]" value="${variation.stock}" /></td></tr>`;
          })
          $('#modal-edit-product .table-variations tbody').append(table_row);
          $('.product-variations').show();
        }else{          
          $("[name=sku]").prop('required', true);
          $("[name=price]").prop('required', true);
          $("[name=stock]").prop('required', true);
          $('.product-format-simple').show();
          $('.product-format-variations').hide();
          $('#modal-edit-product [name="sku"]').val(data.data.info.sku);
          $('#modal-edit-product [name="price"]').val(data.data.info.price);
          $('#modal-edit-product [name="stock"]').val(data.data.info.stock);
          $('.product-simple').append(`<h3>R$ ${data.data.info.price}</h3>
          <p style="margin-top:20px"><strong>Estoque:</strong> ${data.data.info.stock}</p>`);
          $('.product-simple').show();
        }
        waitBodyEnd();
        maskBrl();
      }else{
        $('.title a').hide();
        $('.container-product').append(`<p style="margin-bottom:20px;color:#666"><small>${data.message}</small></p>`);
        waitBodyEnd();
      }
    }
  })
  })

  

  $(document).on("change", "[name='var_colors[]'], [name='var_sizes[]']", function(){
    let colors = $("[name='var_colors[]']").val();
    let sizes = $("[name='var_sizes[]']").val();
    
    let type_variations = null;
    if(colors.length > 0 && sizes.length > 0){
      type_variations = "color_size";
    }else if(colors.length > 0){
      type_variations = "color";
    }else if(sizes.length > 0){
      type_variations = "size";
    }

      let fields_var = `<td class="input-field"><input name="var_sku[]" placeholder="SKU" type="text" /></td><td class="input-field"><input name="var_price[]" type="text" class="mask-brl" placeholder="Preço" /><td class="input-field"><input name="var_stock[]" type="number" placeholder="Quantidade" /></td></td>`;
      if(type_variations == "color"){
        $("#modal-edit-product .table-variations tbody").empty();
        let html_colors = '';
        colors.forEach(color => {          
          html_colors += `<tr><td>${color}<input type="hidden" name="var_color[]" value="${color}" /><input type="hidden" name="var_size[]" value="" /></td>${fields_var}</tr>`;
        });
        $("#modal-edit-product .table-variations tbody").html(html_colors);
      }
      if(type_variations == "size"){
        $("#modal-edit-product .table-variations tbody").empty();
        let html_sizes = '';
        sizes.forEach(size => {
          html_sizes += `<tr><td>${size}<input type="hidden" name="var_color[]" value="" /><input type="hidden" name="var_size[]" value="${size}" /></td>${fields_var}</tr>`;
        });
        $("#modal-edit-product .table-variations tbody").html(html_sizes);
      }
      if(type_variations == "color_size"){
        $("#modal-edit-product .table-variations tbody").html('');
        let html_variations = '';
        colors.forEach(color => {
          sizes.forEach(size => {
            html_variations += `<tr><td>${color} - ${size}<input type="hidden" name="var_color[]" value="${color}" /><input type="hidden" name="var_size[]" value="${size}" /></td>${fields_var}</tr>`;
          });          
          $("#modal-edit-product .table-variations tbody").html(html_variations);
        })
      }
      maskBrl();
  })


var imagePreview = document.querySelector('#modal-edit-product .image-preview');
var imageFields = document.querySelector('#modal-edit-product #imageFields');
var addImageButton = document.querySelector('#modal-edit-product #addImageButton');

var imageCount = 0;

addImageButton.addEventListener('click', function() {  
    if ($('#modal-edit-product .image-preview img').length < 5) {
        var fieldInput = document.createElement('input');
        fieldInput.type = 'file';
        fieldInput.name = 'images[]';
        fieldInput.setAttribute('data-image-id', imageCount);
        fieldInput.accept = 'image/*';
        imageFields.appendChild(fieldInput);
        
        document.querySelector('#modal-edit-product input[data-image-id="' + imageCount + '"]').addEventListener('change', function() {
            var file = fieldInput.files[0];
            if (file) {
                    var imgUrl = window.URL.createObjectURL(file);              
                    var img = `<img src="${imgUrl}" />`;
                    var deleteButton = `<a href="#" class="image-delete" data-id="${imageCount}"><i class="ri-close-line"></i></a>`;
                      imagePreview.innerHTML += `<div class="card-upload-img" data-id="${imageCount}">
                      ${img} ${deleteButton}
                    </div>`;
                    imageCount++;
                    if ($('#modal-edit-product .image-preview img').length >= 5) {
                        addImageButton.style.display = 'none';
                    }
            }
        });
        $('#modal-edit-product input[data-image-id="' + imageCount + '"]').click();

    }
});

$(document).on("click", ".image-delete", function(){
  let id = $(this).data("id");
  $(`.card-upload-img[data-id="${id}"]`).remove();
  $(`[data-image-id="${id}"]`).remove();
  if($('#modal-edit-product .image-preview img').length < 5) {
      addImageButton.style.display = 'block';
  }
});

$(document).on("click", ".image-delete-prev", function(){
  let id = $(this).data("id");
  $(`.card-upload-img[data-img-prev="${id}"]`).remove();
  $(`[data-image-id="${id}"]`).remove();
  // add id separate by comma to input field
  let images_removed = $('#modal-edit-product [name="images_prev_removed"]').val();
  if(images_removed){
    images_removed += `,${id}`;
  }else{
    images_removed = id;
  }
  $('#modal-edit-product [name="images_prev_removed"]').val(images_removed);
  if($('#modal-edit-product .image-preview img').length < 5) {
      addImageButton.style.display = 'block';
  }
});


  $(document).on('click', '.open-modal', function(e) {
    e.preventDefault();
    let img_url = $(this).data('img');
    $('.image-modal').attr('src', img_url);
  });


$(document).on('submit', '#modal-edit-product form', function(e) {
  e.preventDefault();
  waitBody();
  $.ajax({
    type: 'POST',
    url: '/api/products/{{ id }}',
    dataType: 'json',
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function(response) {
      if(response.status){
        window.location.reload();
      }else{
        toastError(response.message);
        waitBodyEnd();
      }
    }
  })
});

$(document).on('click', '.remove-product', function(e) {
  e.preventDefault();
  Swal.fire({
                title: 'Tem certeza que deseja excluir este produto?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, Excluir',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: 'DELETE',
                        url: '/api/products/{{ id }}',
                        dataType: 'json',
                        success: function(response) {
                            if(response.status){
                              Swal.fire('Produto excluído!', '', 'success').then((confirm) => {
                                  if(confirm.isConfirmed){
                                      window.location.href = '/';
                                  }
                              })
                            }
                        }
                    })
                }
            });

});
</script>
{% endblock %}