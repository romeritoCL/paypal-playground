import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let buttonContainer = document.getElementById('paypal-button-container');
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let captureUrl = document.getElementById('capture-url').dataset.captureUrl;
let orderCreateEditorContainer = document.getElementById('create-order-editor');
let paypalButtonsEditorContainer = document.getElementById('paypal-buttons-editor');

let orderCreateJson = {
    intent: 'capture',
    payer: {
        name: {
            given_name: settings['settings-customer-name'],
            surname: settings['settings-customer-family-name'],
        },
        email_address: settings['settings-customer-email'],
        address: {
            address_line_1: settings['settings-customer-street'],
            country_code: settings['settings-customer-country'],
            admin_area_1: settings['settings-customer-province'],
            admin_area_2: settings['settings-customer-city'],
            postal_code: settings['settings-customer-zip-code']
        }
    },
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
            name: name,
            unit_amount: { currency_code: settings['settings-customer-currency'], value: settings['settings-item-price']},
            quantity: 1,
            sku: settings['settings-item-sku'],
            description: settings['settings-item-description']
        }],
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
    label: 'pay'
};

let paypalButtonsEditor = new JSONEditor(
    paypalButtonsEditorContainer,
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
            reloadPayPalButtons();
        }
    },
    paypalButtonsStyle
);
paypalButtonsEditor.expandAll();


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

function reloadPayPalButtons()
{
    $('#paypal-button-container').empty();
    let paypalButtonsStyleObject = paypalButtonsEditor.get();
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create(orderCreateEditor.get());
        },
        style: paypalButtonsStyleObject,
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
}

reloadPayPalButtons();