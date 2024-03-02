{% extends 'layout/layout.twig.php'  %}

{% block title %}Início{% endblock %}

{% block content %}
<style>
    #categories{
        background-color: #fff;
        padding:40px;
        border-radius:10px;
    }
    #categories li{
        list-style: none;
        padding: 10px 0;
    }
</style>
<div class="title">
    <h2 class="section--title">Categorias</h2>
</div>
{% if message %}
<div class="alert alert-success" style="margin-bottom:20px">
  <span>{{ message }}</span>
  <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
</div>
{% endif %}
<div class="container" style="padding-top: 0px;margin-bottom: 40px">
  <a href="javascript:void(0)" class="btn btn-primary open-modal" data-target="modal-add-category">Adicionar Categoria</a>
</div>
<ul id="categories"></ul>


<div id="modal-add-category" class="modal">
  <div class="modal-content" style="width: 500px">
    <div class="modal-header">
      <h3>Adicionar Categoria</h3>
      <span class="btn-close-modal close-modal"><i class="ri-close-line"></i></span>
    </div>
    <div class="modal-body">
    <form id="form-add-category">    
                        <div class="input-field" style="margin-top: 20px">
                            <label>Título</label>
                            <input type="text" name="title" placeholder="Título da categoria" required>
                        </div>
                
                <button class="btn-form">
                        <span class="btnText">Adicionar Categoria</span>
                        <i class="uil uil-navigator"></i>
                    </button>
        </form>
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
                      $('#categories').append(`<li>${category.title} <a href="#" class="text-danger remove-category" data-id="${category.id}"><i class="ri-delete-bin-line"></i></a></li>`);
                  })
                  waitBodyEnd();
              }else{
                $('#categories').append(response.message);
                waitBodyEnd();
              }
          },
          error: function(error) {                
            toastError('Ocorreu um erro ao carregar as categorias');
            $('#categories').append('Ocorreu um erro ao carregar as categorias');
            waitBodyEnd();
          }
      })
  })


  $(document).on('click', '.remove-category', function(e) {
  e.preventDefault();
  let id = $(this).data('id');
  Swal.fire({
                title: 'Tem certeza que deseja excluir esta categoria?',
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
                        url: '/api/category/'+id,
                        dataType: 'json',
                        success: function(response) {
                            if(response.status){
                              Swal.fire('Categoria excluída!', '', 'success').then((confirm) => {
                                  if(confirm.isConfirmed){
                                      window.location.href = '/categories';
                                  }
                              })
                            }else{
                                Swal.fire('Erro!', 'Ocorreu um erro ao excluir a categoria', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('Erro!', 'Ocorreu um erro ao excluir a categoria', 'error');
                        }
                    })
                }
            });

});

$(document).on('submit', '#form-add-category', function(e) {
  e.preventDefault();
  waitBody();
  $.ajax({
    type: 'POST',
    url: '/api/categories',
    data: $(this).serialize(),
    dataType: 'json',
    success: function(response) {
      if(response.status){
        window.location.reload();
      }else{
        toastError(response.message);
        waitBodyEnd();
      }
    }
  })
})
</script>
{% endblock %}