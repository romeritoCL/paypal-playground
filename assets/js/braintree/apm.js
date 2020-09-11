import braintree from 'braintree-web';
import braintreePayments from './payments';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsPaypalClientId = document.querySelector('.js-paypal-client-id');
let paypalClientId = jsPaypalClientId.dataset.paypalClientId;
let submitButtonOne = document.querySelector('#submit-button-one');
let apmAmount = document.getElementById('apm-amount');
let deviceData;

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
            paypal: true
        }, function (err, dataCollectorInstance) {
            deviceData = dataCollectorInstance.deviceData;
        });
        braintree.paypalCheckout.create({
            client: clientInstance
        }, function (paypalCheckoutErr, paypalCheckoutInstance) {
            if (paypalCheckoutErr) {
                console.error('Error creating PayPal Checkout:', paypalCheckoutErr);
                return;
            }
            paypalCheckoutInstance.loadPayPalSDK({
                'client-id': paypalClientId,
                currency: 'EUR',
                intent: 'authorize'
            }, function () {
                paypal.Buttons({
                    fundingSource: paypal.FUNDING.PAYPAL,
                    createOrder: function () {
                        return paypalCheckoutInstance.createPayment({
                            flow: 'checkout',
                            amount: apmAmount,
                            currency: 'EUR',
                            intent: 'authorize',
                            enableShippingAddress: true,
                        });
                    },

                    onApprove: function (data, actions) {
                        return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
                            braintreePayments.sendServerPayLoad(payload,deviceData);
                        });
                    },

                    onCancel: function (data) {
                        console.log('PayPal payment cancelled', JSON.stringify(data, 0, 2));
                    },

                    onError: function (err) {
                        console.error('PayPal error', err);
                    }
                }).render('#apm-container');
            });
        });
    });
});
