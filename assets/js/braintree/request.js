import braintree from 'braintree-web';
import braintreePayments from './payments';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let hostedFieldsForm = document.querySelector('#hosted-fields-form');
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
            button.addEventListener('click', function (event) {
                let amount = '180.00';
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
                    braintreePayments.sendServerPayLoad(payload, deviceData);
                });
            });
        });
    });
} else {
    document.getElementById('PaymentRequestHostedFields').classList.remove('invisible');
    braintree.client.create({
        authorization: clientToken
    }, function (clientErr, clientInstance) {
        if (clientErr) {
            console.error(clientErr);
            return;
        }
        braintree.dataCollector.create({
            client: clientInstance,
            paypal: true
        }, function (err, dataCollectorInstance) {
            deviceData = dataCollectorInstance.deviceData;
        });
        braintree.hostedFields.create({
            client: clientInstance,
            styles: {
                'input': {
                    'font-size': '16px',
                    'color': '#3A3A3A',
                    'font-family': 'monospace'
                },
                '.invalid': {
                    'color': 'red'
                },
                '.valid': {
                    'color': 'green'
                },
                ':focus': {
                    'color': 'blue'
                }
            },
            fields: {
                number: {
                    selector: '#card-number',
                    placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                    selector: '#card-cvv',
                    placeholder: '123'
                },
                expirationDate: {
                    selector: '#card-expiration',
                    placeholder: '10/2022'
                }
            }
        }, function (hostedFieldsErr, hostedFieldsInstance) {
            if (hostedFieldsErr) {
                console.error(hostedFieldsErr);
                return;
            }
            let submitButtonOne = document.querySelector('#submit-button-one');
            submitButtonOne.removeAttribute('disabled');
            hostedFieldsForm.addEventListener('submit', function (event) {
                event.preventDefault();
                hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                    if (tokenizeErr) {
                        console.error(tokenizeErr);
                        return;
                    }
                    braintreePayments.sendServerPayLoad(payload, deviceData);
                });
            }, false);
        });
    });
}
