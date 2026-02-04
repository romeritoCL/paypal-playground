import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;
let returnUrl = document.getElementById('return-url').dataset.returnUrl;
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    intent: 'capture',
    payer: {
        name: {
            given_name: settings['settings-customer-name'],
            surname: settings['settings-customer-family-name'],
        },
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
            name: settings['settings-item-name'],
            unit_amount: { currency_code: settings['settings-customer-currency'], value: settings['settings-item-price']},
            quantity: 1,
            sku: settings['settings-item-sku'],
            description: settings['settings-item-description']
        }],
    }],
    payment_source: {
        paypal: {
            email_address: settings['settings-customer-email'],                            
        experience_context: {
          user_action: "PAY_NOW",
          return_url: returnUrl,
          cancel_url: returnUrl
        }
    }
  }
};

let PayPalButtons = {
    style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'pill',
        label: 'pay'
    },
    appSwitchWhenAvailable: true,
};

paypalPayments.startPayments(orderCreateJson, PayPalButtons);
