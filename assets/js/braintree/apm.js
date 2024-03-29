import braintree from 'braintree-web';
import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsCloseUrl = document.querySelector('.js-braintree-close-url');
let closeUrl = jsCloseUrl.dataset.braintreeCloseUrl;
let jsPaypalClientId = document.querySelector('.js-paypal-client-id');
let paypalClientId = jsPaypalClientId.dataset.paypalClientId;
let submitButtonOne = document.querySelector('#submit-button-one');
let applePayButton = document.querySelector('#apple-pay-button');
let apmAmount = document.getElementById('apm-amount');
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let deviceData, localPaymentInstance;
let serverOptionsObject = {
    options: {
        submitForSettlement: true,
        paypal: {
            customField: 'SubSeller ID',
            description: 'PayPal purchase description'
        }
    }
};
let paypalOrderJsonEditor = document.getElementById('paypal-order-json-editor');
let paypalShippingAmount = 5;
let paypalOrderJson = {
    flow: 'checkout',
    currency: 'EUR',
    intent: 'authorize',
    enableShippingAddress: true,
    shippingAddressEditable: true,
    shippingOptions: [{
        id: 'shipping',
        type: 'SHIPPING',
        label: 'Seur - 24h',
        selected: true,
        amount: {
            value: paypalShippingAmount,
            currency: 'EUR'
        }
    }]
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

const popupBridge = () => {
    let userAgent = window.navigator.userAgent.toLowerCase();
    let safari = /safari/.test(userAgent);
    let ios = /iphone|ipod|ipad/.test(userAgent);
    return ios && !safari;
};

braintreePayments.animatePaymentForm(serverOptionsObject);

function merchantAccountId()
{
    return ("devoralive");
}

function createLocalPaymentClickListener(type)
{
    let countryCode;
    switch (type) {
        case 'ideal':
            countryCode = "NL";
            break;
        case 'multibanco':
            countryCode = "PT";
            break;
        default:
            countryCode = settings['settings-customer-country']
            break;
    }
    return function (event) {
        event.preventDefault();
        localPaymentInstance.startPayment({
            paymentType: type,
            amount: apmAmount,
            fallback: {
                url: popupBridge() ? "com.braintreepayments.popupbridgeexample://popupbridgev1" : closeUrl,
                buttonText: 'Continue',
            },
            currencyCode: 'EUR',
            shippingAddressRequired: true,
            email: 'joe@getbraintree.com',
            phone: '5101231234',
            givenName: 'Joe',
            surname: 'Doe',
            address: {
                streetAddress: 'Oosterdoksstraat 110',
                extendedAddress: 'Apt. B',
                locality: 'DK Amsterdam',
                postalCode: '1011',
                region: 'NH',
                countryCode: countryCode
            },
            onPaymentStart: function (data, start) {
                console.log(data.paymentId);
                start();
            }
        }, function (startPaymentError, payload) {
            if (startPaymentError) {
                if (startPaymentError.code === 'LOCAL_PAYMENT_POPUP_CLOSED') {
                    console.error('Customer closed Local Payment popup.');
                } else {
                    console.error('Error!', startPaymentError);
                }
            } else {
                braintreePayments.sendServerPayLoad(payload, deviceData);
            }
        });
    };
}

$('#ideal-button').on('click', createLocalPaymentClickListener('ideal'));
$('#sofort').on('click', createLocalPaymentClickListener('sofort'));
$('#bancontact-button').on('click', createLocalPaymentClickListener('bancontact'));
$('#trustly-button').on('click', createLocalPaymentClickListener('trustly'));
$('#multibanco-button').on('click', createLocalPaymentClickListener('multibanco'));

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
        braintree.localPayment.create({
            client: clientInstance,
            merchantAccountId: merchantAccountId()
        }, function (localPaymentErr, paymentInstance) {
            if (localPaymentErr) {
                console.error('Error creating local payment:', localPaymentErr);
                return;
            }
            localPaymentInstance = paymentInstance;
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
                            braintreePayments.sendServerPayLoad(payload, deviceData);
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
        let googleButton = document.querySelector('#google-pay-button');
        let paymentsClient = new google.payments.api.PaymentsClient({
            environment: 'TEST'
        });
        let button = paymentsClient.createButton({
            buttonType: 'plain',
            buttonLocale: 'en',
            buttonSizeMode: 'fill',
            onClick: () => {},
        });
        googleButton.appendChild(button);
        braintree.googlePayment.create({
            client: clientInstance,
            googlePayVersion: 2,
            googleMerchantId: 'merchant-id-from-google'
        }, function (googlePaymentErr, googlePaymentInstance) {
            paymentsClient.isReadyToPay({
                // see https://developers.google.com/pay/api/web/reference/object#IsReadyToPayRequest
                apiVersion: 2,
                apiVersionMinor: 0,
                allowedPaymentMethods: googlePaymentInstance.createPaymentDataRequest().allowedPaymentMethods,
                existingPaymentMethodRequired: true // Optional
            }).then(function (response) {
                if (response.result) {
                    googleButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        let paymentDataRequest = googlePaymentInstance.createPaymentDataRequest({
                            transactionInfo: {
                                currencyCode: 'EUR',
                                totalPriceStatus: 'FINAL',
                                totalPrice: apmAmount // Your amount
                            }
                        });

                        let cardPaymentMethod = paymentDataRequest.allowedPaymentMethods[0];
                        cardPaymentMethod.parameters.billingAddressRequired = true;
                        cardPaymentMethod.parameters.billingAddressParameters = {
                            format: 'FULL',
                            phoneNumberRequired: true
                        };

                        paymentsClient.loadPaymentData(paymentDataRequest).then(paymentData => {
                            googlePaymentInstance.parseResponse(paymentData, function (err, result) {
                                if (err) {
                                    console.log(err);
                                }

                                braintreePayments.sendServerPayLoad(paymentData, deviceData);
                                let destroy = document.getElementById('apm-buttons-destroyable');
                                destroy.parentNode.removeChild(destroy);
                            });
                        }).catch(function (err) {
                            console.log(err);
                        });
                    });
                    $('#google-pay-button').removeClass('d-none');
                }
            }).catch(function (err) {
                console.log(err);
            });
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
                let apmAmountObject = {'amount': apmAmount};
                let paypalOrderObject = customerJsonEditor.get();
                let finalPayPalObject = {...apmAmountObject, ...paypalOrderObject}
                paypal.Buttons({
                    style: {
                        shape:  'pill',
                        label:  'paypal'
                    },
                    fundingSource: paypal.FUNDING.PAYPAL,
                    createOrder: function () {
                        return paypalCheckoutInstance.createPayment(finalPayPalObject);
                    },
                    onApprove: function (data) {
                        return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
                            let destroy = document.getElementById('apm-buttons-destroyable');
                            destroy.parentNode.removeChild(destroy);
                            braintreePayments.sendServerPayLoad(payload, deviceData);
                        });
                    },
                    onShippingChange: function (data) {
                        if (data.shipping_address.country_code !== 'ES') {
                            let newShippingAmount = 25.00;
                            paypalShippingAmount = newShippingAmount - paypalShippingAmount;
                            apmAmount = parseFloat(apmAmount) + parseFloat(paypalShippingAmount);
                        }
                        return paypalCheckoutInstance.updatePayment({
                            currency: 'EUR',
                            amount: apmAmount,
                            lineItems: [],           // Required
                            paymentId: data.paymentId,  // Required
                            shippingOptions: [{
                                id: 'shipping',
                                type: 'SHIPPING',
                                label: 'Seur - 24h',
                                selected: true,
                                amount: {
                                    value: paypalShippingAmount,
                                    currency: 'EUR'
                                }
                            }]
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
        $('#ideal-button').removeClass('d-none');
        $('#sofort-button').removeClass('d-none');
        $('#trustly-button').removeClass('d-none');
        $('#bancontact-button').removeClass('d-none');
        $('#multibanco-button').removeClass('d-none');
    });
});
