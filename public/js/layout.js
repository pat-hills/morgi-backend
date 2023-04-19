function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showSuccessMessage(message) {
    $('#success-alert-message').text(message);
    $("#success-alert").fadeTo(5000, 500).slideUp(500, function () {
        $("#success-alert").slideUp(2000);
    });
}

function showErrorMessage(message) {
    $('#error-alert-message').text(message);
    $("#error-alert").fadeTo(5000, 500).slideUp(500, function () {
        $("#error-alert").slideUp(2000);
    });
}
