import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    intent: 'capture',
    purchase_units: [{
        payee: {
            email_address: settings['settings-merchant-email'],
        },
        reference_id: settings['settings-item-sku'] + "-first-seller",
        amount: {
            currency_code: settings['settings-customer-currency'],
            value: 35,
        },
        description:  "First Seller Order"
    }, {
        payee: {
            email_address: 'sb-zz71r6215096@business.example.com',
        },
        reference_id: settings['settings-item-sku'] + "-second-seller",
        amount: {
            currency_code: settings['settings-customer-currency'],
            value: 50,
        },
        description:  "Second Seller Order"
    }],
    application_context: {
        shipping_preference: "NO_SHIPPING",
    }
};

let paypalButtonsStyle = {
    layout: 'vertical',
    color: 'black',
    shape: 'rect',
    label: ''
};

paypalPayments.startPayments(orderCreateJson, paypalButtonsStyle);
