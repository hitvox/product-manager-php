const switchModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        const actualDisplay = window.getComputedStyle(modal).getPropertyValue('display');
        modal.style.display = (actualDisplay === 'none') ? 'block' : 'none';
    }
  }
  
$(document).on('click', '.open-modal', function(e) {
    e.preventDefault();
    const modalId = $(this).data('target');
    switchModal(modalId);
});

$(document).on('click', '.close-modal', function(e) {
    e.preventDefault();
    const modalId = $(this).closest('.modal').attr('id');
    switchModal(modalId);
});
  

  window.onclick = function(event) {
      const modal = document.querySelector('.modal');
      const modal_close_bg = document.querySelector('.modal-close-bg');
    if (event.target == modal && event.target.contains(modal_close_bg)) {
      let modalId = event.target.getAttribute('id');
      switchModal(modalId);
    }
  }