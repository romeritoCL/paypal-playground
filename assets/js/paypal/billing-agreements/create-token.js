let createToken = {};
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);

createToken.createBillingAgreementTokenBody = {
    description: "Billing Agreement",
    shipping_address: {
        line1: settings['settings-customer-street'],
        city: settings['settings-customer-city'],
        state: settings['settings-customer-province'],
        postal_code: settings['settings-customer-zip-code'],
        country_code: settings['settings-customer-country'],
        recipient_name: settings['settings-customer-name']
    },
    payer: {
        payment_method: "PAYPAL"
    },
    plan: {
        type: "MERCHANT_INITIATED_BILLING",
        merchant_preferences: {
            return_url: "https://example.com/return",
            cancel_url: "https://example.com/cancel",
            notify_url: "https://example.com/notify",
            accepted_pymt_type: "INSTANT",
            skip_shipping_address: false,
            immutable_shipping_address: true
        }
    }
};

createToken.startEditor = function () {
    let createTokenEditorContainer = document.getElementById('create-token-editor');
    let createTokenEditor = new JSONEditor(
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
    this.jsonEditor = createTokenEditor;
}

createToken.getEditor = function () {
    return this.editor;
}

export default createToken;