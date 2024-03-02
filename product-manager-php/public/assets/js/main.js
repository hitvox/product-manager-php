let menu = document.querySelector('.menu')
let sidebar = document.querySelector('.sidebar')
let mainContent = document.querySelector('.main--content')
menu.onclick = function() {
    sidebar.classList.toggle('active')
    mainContent.classList.toggle('active')
}

const waitBody = () => {
    $('body').waitMe({
        effect : 'rotation',
        bg: 'rgba(255,255,255,0.7)',
        color : '#4070f4',
    });
}

const waitBodyEnd = () => {
    $('body').waitMe('hide');
}

const toastSuccess= (msg) => {
    Toastify({
    text: msg,
    duration: 3000,
    close: true,
    gravity: "top",
    position: "right",
    stopOnFocus: true,
    style: {
        background: "#35bcbf",
        borderRadius: "8px",
    }
    }).showToast();
}

const toastError = (msg)=>{
    Toastify({
    text: msg,
    duration: 3000,
    close: true,
    gravity: "top",
    position: "right",
    stopOnFocus: true,
    style: {
        background: "#f95959",
        borderRadius: "8px",
    }
    }).showToast();
}

const maskBrl = ()=>{
    $(() => {
        $('.mask-brl').maskMoney({allowNegative: true, thousands:'.', decimal:',', affixesStay: false});
    });
}

maskBrl();


$(document).ready(function() {            
    $(".select2-tags").select2({
        tags: true,
        language: {
        noResults: function () {
            return "Digite para adicionar...";
        }
        }
    });
});