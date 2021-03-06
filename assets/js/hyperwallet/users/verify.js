let userToken = $('.js-user-token').data('userToken');
let userLanguage = $('.js-user-language').data('userLanguage');
let url = 'https://api.sandbox.hyperwallet.com/rest/widgets/users/'+ userToken +'/'+ userLanguage +'.v2_3_0.min.js';
let authUrl = $('.js-user-auth-token-url').data('userAuthTokenUrl');
authUrl = authUrl.replace("user_token_replace", userToken);

function getAuthenticationToken(callback)
{
    $.ajax({
        url: authUrl,
        method: 'GET',
        dataType: 'json',
        beforeSend: function (xhrObj) {
            xhrObj.setRequestHeader("Content-Type","application/json");
            xhrObj.setRequestHeader("Accept","application/json"); },
        success: function ( data, txtStatus, xhr ) {
            callback(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
            callback(null, errorThrown);
        }
    });
}

$.getScript(url, function () {
    HWWidgets.initialize(function (onSuccess, onFailure) {
        getAuthenticationToken((response) => {
            onSuccess(response);
        });
    });

    HWWidgets.users.configure({
        "template": "bootstrap3",
        "el": document.getElementById("VerificationUI")
    }).display();
});
