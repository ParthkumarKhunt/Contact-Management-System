

function showToster(status, message) {
    // alert("Hello");
    toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000
    };
    if (status == 'success') {
        toastr.success(message, 'Success');
    }
    if (status == 'error') {
        toastr.error(message, 'Fail');

    }
    if (status == 'warning') {
        toastr.warning(message, 'Warning');

    }
}


function handleAjaxResponse(output) {

    output = JSON.parse(output);

    if (output.message != '') {
        showToster(output.status, output.message, '');
    }
    if (typeof output.redirect !== 'undefined' && output.redirect != '') {
        setTimeout(function() {
            window.location.href = output.redirect;
        }, 4000);
    }
    if (typeof output.reload !== 'undefined' && output.reload != '') {
        setTimeout(function() {
            window.location.reload();
        }, 4000);
    }
    if (typeof output.jscode !== 'undefined' && output.jscode != '') {
        eval(output.jscode);
    }
}
