import braintree from 'braintree-web';
import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsPaypalClientId = document.querySelector('.js-paypal-client-id');
let paypalClientId = jsPaypalClientId.dataset.paypalClientId;
let submitButtonOne = document.querySelector('#submit-button-one');
let applePayButton = document.querySelector('#apple-pay-button');
let apmAmount = document.getElementById('apm-amount');
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let deviceData;

let paypalOrderJsonEditor = document.getElementById('paypal-order-json-editor');
let paypalOrderJson = {
    flow: 'checkout',
    currency: 'EUR',
    intent: 'authorize',
    enableShippingAddress: true,
    shippingAddressEditable: false,
    shippingAddressOverride: {
        recipientName: 'John McMillan',
        line1: '5 Fifth Avenue',
        line2: '3F',
        city: 'Chicago',
        countryCode: 'US',
        postalCode: '60652',
        state: 'IL',
        phone: '123.456.7890'
    }
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
            paypal: true
        }, function (err, dataCollectorInstance) {
            deviceData = dataCollectorInstance.deviceData;
        });
        if (window.ApplePaySession && ApplePaySession.supportsVersion(3) && ApplePaySession.canMakePayments()) {
            braintree.applePay.create({
                client: clientInstance
            }, function (applePayErr, applePayInstance) {
                if (applePayErr) {
                    console.log('Error creating applePayInstance:', applePayErr);
                    return;
                }
                applePayButton.addEventListener('click', function () {
                    let paymentRequest = applePayInstance.createPaymentRequest({
                        total: {
                            label: settings['settings-merchant-name'],
                            amount: apmAmount
                        },
                        currencyCode: settings['settings-customer-currency'],
                        requiredBillingContactFields: ["postalAddress"]
                    });
                    paymentRequest.countryCode = settings['settings-customer-country']
                    paymentRequest.currencyCode = settings['settings-customer-currency'];
                    let session = new ApplePaySession(3, paymentRequest);
                    session.onvalidatemerchant = function (event) {
                        applePayInstance.performValidation({
                            validationURL: event.validationURL,
                            displayName: settings['settings-merchant-name']
                        }, function (err, merchantSession) {
                            if (err) {
                                alert('Apple Pay failed to load.');
                                return;
                            }
                            session.completeMerchantValidation(merchantSession);
                        });
                    };
                    session.onpaymentauthorized = function (event) {
                        applePayInstance.tokenize({
                            token: event.payment.token
                        }, function (tokenizeErr, payload) {
                            if (tokenizeErr) {
                                console.log('Error tokenizing Apple Pay:', tokenizeErr);
                                session.completePayment(ApplePaySession.STATUS_FAILURE);
                                return;
                            }
                            braintreePayments.sendServerPayLoad(payload,deviceData);
                            session.completePayment(ApplePaySession.STATUS_SUCCESS);
                            let destroy = document.getElementById('apm-buttons-destroyable');
                            destroy.parentNode.removeChild(destroy);
                        });
                    };
                    session.begin();
                });
                $('#apple-pay-button').removeClass('d-none');
            });
        }
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
                let apmAmountObject = {'amount': apmAmount};
                let paypalOrderObject = customerJsonEditor.get();
                let finalPayPalObject = {...apmAmountObject, ...paypalOrderObject}
                paypal.Buttons({
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
                }).render('#apm-container');
            });
        });
    });
});
