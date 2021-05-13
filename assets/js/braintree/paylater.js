import braintree from 'braintree-web';
import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let submitButtonOne = document.querySelector('#submit-button-one');
let apmAmount = document.getElementById('apm-amount');
let jsPaypalClientId = document.querySelector('.js-paypal-client-id');
let paypalClientId = jsPaypalClientId.dataset.paypalClientId;
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let deviceData;

let paypalOrderJsonEditor = document.getElementById('paypal-order-json-editor');
let paypalOrderJson = {
    flow: 'checkout',
    currency: 'GBP',
    amount: '123,45',
    intent: 'authorize'
};

let customerJsonEditor = new JSONEditor(
    paypalOrderJsonEditor,
    {
        limitDragging: true,
        name: 'CustomerInfo',
        modes: ['code'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false
    },
    paypalOrderJson
);
braintreePayments.animatePaymentForm();

submitButtonOne.addEventListener('click', function () {
    apmAmount = apmAmount.value;
    let destroy = document.getElementById('apm-creation-destroyable');
    destroy.parentNode.removeChild(destroy);
    braintree.client.create({
        authorization: clientToken
    }, function (clientErr, clientInstance) {
        if (clientErr) {
            console.error('Error creating client:', clientErr);
            return;
        }
        braintree.dataCollector.create({
            client: clientInstance,
            kount: true,
            paypal: true
        }, function (err, dataCollectorInstance) {
            deviceData = JSON.parse(dataCollectorInstance.deviceData);
        });
        braintree.paypalCheckout.create({
            client: clientInstance
        }, function (paypalCheckoutErr, paypalCheckoutInstance) {
            if (paypalCheckoutErr) {
                console.error('Error creating PayPal Checkout:', paypalCheckoutErr);
                return;
            }
            paypalCheckoutInstance.loadPayPalSDK({
                'buyer-country': 'GB',
                components: 'buttons,messages',
                currency: 'GBP',
                intent: 'authorize',
                dataAttributes: {
                    amount: apmAmount
                }
            }, function () {
                let apmAmountObject = {'amount': apmAmount};
                let paypalOrderObject = customerJsonEditor.get();
                let finalPayPalObject = {...apmAmountObject, ...paypalOrderObject}
                let button = paypal.Buttons({
                    style: {
                        "layout": "vertical",
                        "color": "gold",
                        "shape": "pill",
                        "label": "pay"
                    },
                    fundingSource: paypal.FUNDING.PAYPAL,
                    createOrder: function () {
                        return paypalCheckoutInstance.createPayment(finalPayPalObject);
                    },
                    onApprove: function (data) {
                        return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
                            let destroy = document.getElementById('apm-buttons-destroyable');
                            destroy.parentNode.removeChild(destroy);
                            braintreePayments.sendServerPayLoad(payload,deviceData);
                        });
                    },

                    onCancel: function (data) {
                        console.log('PayPal payment cancelled', JSON.stringify(data, 0, 2));
                    },

                    onError: function (err) {
                        console.error('PayPal error', err);
                    }

                });
                if (!button.isEligible()) {
                    console.log('Not eligible');
                }
                button.render('#apm-container');
            });
        });
    });
});
