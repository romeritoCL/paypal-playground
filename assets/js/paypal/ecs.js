let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let z = document.getElementById('ecs-result-url').dataset.ecsResultUrl;

let orderCreateJson = {
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
};

let paypalButtonsStyle = {
    layout: 'vertical',
    color: 'gold',
    shape: 'pill',
    label: 'buynow'
};

paypal.Buttons({
    fundingSource: paypal.FUNDING.PAYPAL,
    createOrder: function (data, actions) {
        return actions.order.create(orderCreateJson);
    },
    style: paypalButtonsStyle,
    onShippingChange: function () {
    },
    onApprove: function (data) {
        ecsResultUrl = ecsResultUrl.replace("payment_id_status", data.orderID);
        jQuery.post(
            ecsResultUrl,
            null,
            function (data) {
                document.getElementById('item-article-body').innerHTML = data
            }
        );
    }
}).render('#paypal-button-container');
