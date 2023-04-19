import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
window.JSONEditor = JSONEditor;

let captureUrl = document.getElementById('capture-url').dataset.captureUrl;
let createUrl = document.getElementById('create-url').dataset.createUrl;
let selfUrl = document.getElementById('self-url').dataset.selfUrl;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    application_context: {
        shipping_preference: "SET_PROVIDED_ADDRESS"
    },
    intent: 'CAPTURE',
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
        shipping: {
            address: {
                address_line_1: 'Gran Via 32, 2º',
                admin_area_2: 'Madrid',
                admin_area_1: 'Madrid',
                postal_code: 28008,
                country_code: 'ES'
            }
        }
    }],
    payment_source: {
        paypal: {
            attributes: {
                vault: {
                    store_in_vault: "ON_SUCCESS",
                    usage_type: "MERCHANT",
                    customer_type: "CONSUMER"
                }
            },
            experience_context: {
                return_url: selfUrl,
                cancel_url: selfUrl
            }
        }
    }
};


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
    onInit(data, actions)  {
        actions.disable();
        let checkbox = document.getElementById('flexCheckDefault');
        checkbox.addEventListener('change', function (event) {
            // Enable or disable the button when it is checked or unchecked
            if (event.target.checked) {
                actions.enable();
            } else {
                actions.disable();
            }
        });
    },
    onClick()  {
        let checkbox = document.getElementById('flexCheckDefault');
        if (!checkbox.checked) {
            alert('Please accept the Terms & Conditions');
        }
    },
    createOrder: function () {
        let body = orderCreateEditor.get();
        return fetch(createUrl, {
            method: 'post',
            body: JSON.stringify(body),
        }).then(function (response) {
            return response.json();
        }).then(function (orderData) {
            return orderData.id;
        });
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
