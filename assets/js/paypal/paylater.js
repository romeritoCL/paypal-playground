let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let captureUrl = document.getElementById('capture-url').dataset.captureUrl;

paypal.Buttons({
    createOrder: function (data, actions) {
        return actions.order.create({
            intent: 'capture',
            purchase_units: [{
                reference_id: settings['settings-item-sku'],
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: (+settings['settings-item-price'] + +settings['settings-item-shipping'])
                },
                description: settings['settings-item-description']
            }],
            application_context: {
                brand_name: settings['settings-merchant-name'],
                user_action: "PAY_NOW"
            }
        })
    },
    style: {
        shape: 'rect',
        color: 'gold',
        layout: 'vertical',
        label: 'paypal',
    },
    fundingSource: paypal.FUNDING.PAYPAL,
    onApprove: function (data) {
        captureUrl = captureUrl.replace("payment_id_status", data.orderID);
        jQuery.post(
            captureUrl,
            null,
            function (data) {
                document.getElementById('result-col').innerHTML = data
            }
        );
    }
}).render('#paypal-button-container');