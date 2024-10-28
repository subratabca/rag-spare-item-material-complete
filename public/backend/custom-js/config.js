function showLoader() {
    //document.getElementById('loader').classList.remove('d-none')
    //const loadingSpinner = document.getElementById('bouncing-loader');
    document.getElementById('bouncing-loader').style.display = 'flex';
}
function hideLoader() {
    //document.getElementById('loader').classList.add('d-none')
    document.getElementById('bouncing-loader').style.display = 'none';  
}



function successToast(msg) {
    Toastify({
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        text: msg,
        className: "mb-5",
        style: {
            background: "green",
        }
    }).showToast();
}

function errorToast(msg) {
    Toastify({
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        text: msg,
        className: "mb-5",
        style: {
            background: "red",
        }
    }).showToast();
}


