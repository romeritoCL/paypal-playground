import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;
let returnUrl = document.getElementById('return-url').dataset.returnUrl;
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    intent: 'capture',
    purchase_units: [
        {
            amount: {
                value: settings['settings-item-price'],
                currency_code: settings['settings-customer-currency']
            }
        }
    ],
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
        color:  'black',
        shape:  'rect',
        label:  'paypal'
    },
    appSwitchWhenAvailable: true,
};

paypalPayments.startPayments(orderCreateJson, PayPalButtons);
