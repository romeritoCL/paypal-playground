$("#login-form").submit(function (event) {
    let recaptcha = $("#g-recaptcha-response").val();
    if (recaptcha === "") {
        event.preventDefault();
        $("#g-recaptcha").effect("shake");
    }
});

window.login = function ()
{
    $('#login-form').submit();
}