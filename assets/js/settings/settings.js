let submitButton = document.getElementById('submit-button');
let clearButton = document.getElementById('clear-button');
let jsSettingsSaveUrl = document.querySelector('.js-settings-save-url');
let settingsSaveUrl = jsSettingsSaveUrl.dataset.settingsSaveUrl;
let jsSettingsClearUrl = document.querySelector('.js-settings-clear-url');
let settingsClearUrl = jsSettingsClearUrl.dataset.settingsClearUrl;

submitButton.addEventListener('click', function () {
    submitButton.disabled = true;
    let formData = $('#settings-data').find("select, input").serialize();
    $.ajax({
        url: settingsSaveUrl,
        type: "POST",
        data: formData,
        success: function (data) {
            $('#settings-save-success-alert').removeClass('d-none');
            setTimeout(function () {
                submitButton.disabled = false },2000);
        },
        error: function () {
            alert('failed');
            setTimeout(function () {
                submitButton.disabled = false},2000);
        }
    });
});

clearButton.addEventListener('click', function () {
    if (window.confirm("All settings will be set to default values. Are you sure?")) {
        clearButton.disabled = true;
        $.ajax({
            url: settingsClearUrl,
            type: "DELETE",
            success: function (data) {
                location.reload();
            },
            error: function () {
                alert('Error clearing the Settings');
                setTimeout(function () {
                    clearButton.disabled = false},2000);
            }
        });
    }
});