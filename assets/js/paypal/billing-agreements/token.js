let token = {};
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let createTokenSubmit= document.getElementById('create-token-submit');
let goToApproval= document.getElementById('step-1-submit');

token.createBillingAgreementTokenBody = {
    description: "Billing Agreement Example, between PayPalPlayGround and your PayPal account",
    shipping_address: {
        line1: settings['settings-customer-street'],
        city: settings['settings-customer-city'],
        state: settings['settings-customer-province'],
        postal_code: settings['settings-customer-zip-code'],
        country_code: settings['settings-customer-country'],
        recipient_name: settings['settings-customer-name'] + ' ' + settings['settings-customer-family-name']
    },
    payer: {
        payment_method: "PAYPAL"
    },
    plan: {
        type: "MERCHANT_INITIATED_BILLING",
        merchant_preferences: {
            return_url: window.location.href,
            cancel_url: window.location.href,
            notify_url: window.location.href,
            accepted_pymt_type: "INSTANT",
            skip_shipping_address: false,
            immutable_shipping_address: true
        }
    }
};

token.showResult = function (resultJson) {
    let createTokenResultEditorContainer = document.getElementById('create-token-result');
    this.jsonEditorResult = new JSONEditor(
        createTokenResultEditorContainer,
        {
            limitDragging: true,
            name: 'Result',
            mode: 'view',
            mainMenuBar: false,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
        },
        resultJson
    );
    this.jsonEditorResult.expandAll();

}

token.startEditor = function () {
    let createTokenEditorContainer = document.getElementById('create-token-editor');
    this.jsonEditor = new JSONEditor(
        createTokenEditorContainer,
        {
            limitDragging: true,
            name: 'Billing Agreement',
            modes: ['code'],
            mainMenuBar: false,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
        },
        this.createBillingAgreementTokenBody
    );
}

token.goToApproval = function () {
    let resultJson = this.jsonEditorResult.get()
    window.location.href = resultJson.links[0].href;
}

createTokenSubmit.addEventListener('click', function () {
    this.disabled = true;
    let tokenCreateUrl = document.getElementById('token-create-url').dataset.tokenCreateUrl;
    $.ajax({
        type: "POST",
        url: tokenCreateUrl,
        data: JSON.stringify(token.jsonEditor.get()),
        dataType: "json",
        success:  function (data) {
            token.showResult(JSON.parse(data));
            document.getElementById('step-1-submit').disabled = false;
        }
    });
});

goToApproval.addEventListener('click', function () {
    token.goToApproval();
});

export default token;