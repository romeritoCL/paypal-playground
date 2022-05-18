import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
window.JSONEditor = JSONEditor;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    intent: 'capture',
    purchase_units: [{
        reference_id: settings['settings-item-sku'],
        amount: {
            currency_code: settings['settings-customer-currency'],
            value: (+settings['settings-item-price'] + +settings['settings-item-shipping']),
            breakdown: {
                item_total: {
                    value: settings['settings-item-price'],
                    currency_code: settings['settings-customer-currency']
                },
                shipping: {
                    value: (+settings['settings-item-shipping'] * 2),
                    currency_code: settings['settings-customer-currency']
                },
                shipping_discount: {
                    value: settings['settings-item-shipping'],
                    currency_code: settings['settings-customer-currency']
                }
            }
        },
        description: settings['settings-item-description'],
        items: [{
            name: settings['settings-item-name'],
            unit_amount: { currency_code: settings['settings-customer-currency'], value: settings['settings-item-price']},
            quantity: 1,
            sku: settings['settings-item-sku'],
            description: settings['settings-item-description']
        }],
    }],
};

let captureUrl = document.getElementById('capture-url').dataset.captureUrl;
let orderCreateEditorContainer = document.getElementById('create-order-editor');

let orderCreateEditor = new JSONEditor(
    orderCreateEditorContainer,
    {
        limitDragging: true,
        name: 'ButtonSettings',
        modes: ['code','form'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false,
        onChange: function () {
            reloadPayPalButtons();
        }
    },
    orderCreateJson
);

paypal.Buttons({
    createOrder: function (data, actions) {
        return actions.order.create(orderCreateEditor.get());
    },
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
