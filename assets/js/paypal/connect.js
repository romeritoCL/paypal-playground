import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let submitButtonTwo = document.querySelector('#submit-button-two');
let submitButtonThree = document.querySelector('#submit-button-three');
let stepOneSubmitButton = document.querySelector('#step-1-submit');
let stepTwoSubmitButton = document.querySelector('#step-2-submit');
let stepThreeSubmitButton = document.querySelector('#step-3-submit');
let paypalSDKClientId = document.getElementById('paypal-client-id').dataset.paypalClientId;
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let returnUrl = document.getElementById('return-url').dataset.returnUrl;
let authTokenUrl = document.getElementById('auth-token-url').dataset.authTokenUrl;
let loginUrl = document.getElementById('login-url').dataset.loginUrl;
let customerJsonContainer = document.getElementById('paypal-button-editor');
let urlParams = new URLSearchParams(window.location.search);
let paypalConnectCode = urlParams.get('code');
let paypalConnectSettings = {
    "appid": paypalSDKClientId,
    "authend":"sandbox",
    "scopes":"email profile address openid",
    "containerid":"paypal-button",
    "responseType":"code id_Token",
    "locale": settings['settings-customer-locale'].toLowerCase(),
    "buttonType":"CWP",
    "buttonShape":"pill",
    "buttonSize":"lg",
    "fullPage":"true",
    "returnurl": returnUrl
};

function runStepOne()
{
    function reloadPayPalConnectButton()
    {
        paypal.use(['login'], function (login) {
            login.render(customerJsonEditor.get());
        });
    };

    let customerJsonEditor = new JSONEditor(
        customerJsonContainer,
        {
            limitDragging: true,
            name: 'ButtonSettings',
            modes: ['form','code'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
            onChange: function () {
                reloadPayPalConnectButton();
            }
        },
        paypalConnectSettings
    );
    customerJsonEditor.expandAll();
    reloadPayPalConnectButton();
}

function runStepTwo()
{
    $('#collapseTwo').collapse(true);
    stepOneSubmitButton.disabled = true;
    document.getElementById('paypal-connect-code').value = paypalConnectCode;
}

if (paypalConnectCode) {
    runStepTwo();
} else {
    runStepOne();
}

stepTwoSubmitButton.addEventListener('click', function () {
    $('#collapseThree').collapse(true);
    stepTwoSubmitButton.disabled = true;
});

submitButtonTwo.addEventListener('click', function () {
    $.post(
        authTokenUrl,
        {
            'auth_token': paypalConnectCode
        },
        function (data) {
            document.getElementById('step-two-result-row').innerHTML = data
            let refreshTokenInput = document.getElementById('template-result-id');
            document.getElementById('refresh-token').value = refreshTokenInput.value;
            refreshTokenInput.parentNode.removeChild(refreshTokenInput);
            stepTwoSubmitButton.disabled=false;
        }
    );
})

stepThreeSubmitButton.addEventListener('click', function () {
    location.reload();
});

submitButtonThree.addEventListener('click', function () {
    let refreshToken = document.getElementById('refresh-token').value;
    $.post(
        loginUrl,
        {
            'refresh_token': refreshToken
        },
        function (data) {
            document.getElementById('step-three-result-row').innerHTML = data
            let emailInput = document.getElementById('template-result-id');
            document.getElementById('email').value = emailInput.value;
            emailInput.parentNode.removeChild(emailInput);
            stepThreeSubmitButton.disabled=false;
        }
    );
})