import braintree from 'braintree-web';
import braintreePayments from './payments';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let requestAmount = document.getElementById('request-amount');
let deviceData;

//window.PaymentRequest = braintree.paymentRequest;
braintreePayments.animatePaymentForm();

if (window.PaymentRequest) {
    document.getElementById('PaymentRequestButton').classList.remove('invisible');
    let button = document.querySelector('#payment-request-button');
    braintree.client.create({
        authorization: clientToken
    }, function (clientErr, clientInstance) {
        if (clientErr) {
            alert(clientErr);
            return;
        }
        braintree.dataCollector.create({
            client: clientInstance,
            paypal: true
        }, function (err, dataCollectorInstance) {
            deviceData = dataCollectorInstance.deviceData;
        });
        braintree.paymentRequest.create({
            client: clientInstance,
            googlePayVersion: 2
        }, function (err, instance) {
            if (err) {
                alert(err);
                return;
            }
            requestAmount = requestAmount.value;
            button.addEventListener('click', function (event) {
                let amount = requestAmount;
                instance.tokenize({
                    details: {
                        total: {
                            label: 'Total',
                            amount: {
                                currency: 'EUR',
                                value: amount
                            }
                        }
                    }
                }, function (err, payload) {
                    if (err) {
                        console.error(err);
                        return;
                    }
                    let destroy = document.getElementById('request-creation-destroyable');
                    destroy.parentNode.removeChild(destroy);
                    braintreePayments.sendServerPayLoad(payload, deviceData);
                });
            });
        });
    });
} else {
    document.getElementById('PaymentRequestHostedFields').classList.remove('invisible');
}
