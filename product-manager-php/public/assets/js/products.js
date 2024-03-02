
$(document).on("change", "[name=product_format]", function(){
    let format = $("[name=product_format]").val();
    if(format == "variation"){
      $(".product-format-simple").css("display", "none");
      $(".product-format-variations").css("display", "block");
      // remove required fields
      $("[name=sku]").removeAttr("required");
      $("[name=price]").removeAttr("required");
      $("[name=stock]").removeAttr("required");
    }else{
      $(".product-format-simple").css("display", "block");
      $(".product-format-variations").css("display", "none");
      // add required fields
      $("[name=sku]").prop('required', true);
      $("[name=price]").prop('required', true);
      $("[name=stock]").prop('required', true);
    }
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

    $(".table-variations").css("display", "none");
    if(type_variations){
      $(".table-variations").css("display", "block");
      let fields_var = `<td class="input-field"><input name="var_sku[]" placeholder="SKU" type="text" /></td><td class="input-field"><input name="var_price[]" type="text" class="mask-brl" placeholder="Preço" /><td class="input-field"><input name="var_stock[]" type="number" placeholder="Quantidade" /></td></td>`;
      if(type_variations == "color"){
        $(".table-variations tbody").empty();
        let html_colors = '';
        colors.forEach(color => {          
          html_colors += `<tr><td>${color}<input type="hidden" name="var_color[]" value="${color}" /><input type="hidden" name="var_size[]" value="" /></td>${fields_var}</tr>`;
        });
        $(".table-variations tbody").html(html_colors);
      }
      if(type_variations == "size"){
        $(".table-variations tbody").empty();
        let html_sizes = '';
        sizes.forEach(size => {
          html_sizes += `<tr><td>${size}<input type="hidden" name="var_color[]" value="" /><input type="hidden" name="var_size[]" value="${size}" /></td>${fields_var}</tr>`;
        });
        $(".table-variations tbody").html(html_sizes);
      }
      if(type_variations == "color_size"){
        $(".table-variations tbody").html('');
        let html_variations = '';
        colors.forEach(color => {
          sizes.forEach(size => {
            html_variations += `<tr><td>${color} - ${size}<input type="hidden" name="var_color[]" value="${color}" /><input type="hidden" name="var_size[]" value="${size}" /></td>${fields_var}</tr>`;
          });          
          $(".table-variations tbody").html(html_variations);
        })
      }
      maskBrl();
    }
  })


var imagePreview = document.getElementById('imagePreview');
var imageFields = document.getElementById('imageFields');
var addImageButton = document.getElementById('addImageButton');

var imageCount = 0;

addImageButton.addEventListener('click', function() {
    if (imageCount < 5) {
        var fieldInput = document.createElement('input');
        fieldInput.type = 'file';
        fieldInput.name = 'images[]';
        fieldInput.setAttribute('data-image-id', imageCount);
        fieldInput.accept = 'image/*';
        imageFields.appendChild(fieldInput);
        
        document.querySelector('[data-image-id="' + imageCount + '"]').addEventListener('change', function() {
            var file = fieldInput.files[0];
            if (file) {
                    var imgUrl = window.URL.createObjectURL(file);              
                    var img = `<img src="${imgUrl}" />`;
                    var deleteButton = `<a href="#" class="image-delete" data-id="${imageCount}"><i class="ri-close-line"></i></a>`;
                      imagePreview.innerHTML += `<div class="card-upload-img" data-id="${imageCount}">
                      ${img} ${deleteButton}
                    </div>`;
                    imageCount++;
                    if (imageCount >= 5) {
                        addImageButton.style.display = 'none';
                    }
            }
        });
        $('[data-image-id="' + imageCount + '"]').click();

    }
});

$(document).on("click", ".image-delete", function(){
  let id = $(this).data("id");
  $(`.card-upload-img[data-id="${id}"]`).remove();
  $(`[data-image-id="${id}"]`).remove();
});

$('#form-add-product').on('submit', function(e){
  e.preventDefault();
  waitBody();
  $.ajax({
    url: '/api/products',
    type: 'POST',
    dataType: 'json',
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function(response){
        if(response.status){
            location.reload();
        }else{
            toastError(response.message);
            waitBodyEnd();
        }
    },
    error: function(error){
        
    }
  })
})