import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);

let orderCreateJson = {
    intent: 'capture',
    purchase_units: [{
        reference_id: settings['settings-item-sku'],
        amount: {
            currency_code: settings['settings-customer-currency'],
            value: settings['settings-item-price'],
        },
        description: settings['settings-item-description'],
        shipping: {
            options: [
            {
                id: 1,
                type: "SHIPPING",
                label: "Free Shipping",
                selected: true,
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: 0
                }
            },
            {
                id: 2,
                type: "SHIPPING",
                label: "24h Express",
                selected: false,
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: 30
                }
            },
            {
                id: 4,
                type: "PICKUP",
                label: "Madrid City Shop",
                selected: false,
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: 0
                }
            },
            {
                id: 5,
                type: "PICKUP",
                label: "Paris Rue Rouge Shop",
                selected: false,
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: 3
                }
            },
            {
                id: 6,
                type: "PICKUP",
                label: "Barcelona Les Corts Shop",
                selected: false,
                amount: {
                    currency_code: settings['settings-customer-currency'],
                    value: 3
                }
            }]
        }
    }],
    application_context: {
        brand_name: settings['settings-merchant-name'],
        user_action: "PAY_NOW"
    }
};

let paypalButtonsStyle = {
    layout: 'vertical',
    color: 'blue',
    shape: 'rect',
    label: 'checkout'
};

paypalPayments.startPayments(orderCreateJson, paypalButtonsStyle);
