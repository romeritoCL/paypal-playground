let submitButton = document.getElementById('submit-button');
let jsSettingsUrl = document.querySelector('.js-settings-url');
let settingsUrl = jsSettingsUrl.dataset.settingsUrl;

submitButton.addEventListener('click', function () {
    submitButton.disabled = true;
    let formData = $('#settings-data').find("select, input").serialize();
    console.log(formData);
    $.ajax({
        url: settingsUrl,
        type: "POST",
        data: formData,
        success: function (data) {
            alert('success');
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