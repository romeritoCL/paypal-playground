import braintreePayments from './payments';
import braintree from 'braintree-web';

let stepZeroSubmitButton = document.querySelector('#step-0-submit');
let jsGetCustomerUrl = document.querySelector('.js-get-customer-url');
let getCustomerUrl = jsGetCustomerUrl.dataset.getCustomerUrl;
let jsGetCustomerTokenUrl = document.querySelector('.js-get-customer-token-url');
let getCustomerTokenUrl = jsGetCustomerTokenUrl.dataset.getCustomerTokenUrl;
let clientToken, customerId, deviceData;

braintreePayments.animatePaymentForm();

stepZeroSubmitButton.addEventListener('click', function () {
    getCustomerTokenUrl = getCustomerTokenUrl.replace("customer_id_replace", customerId);
    $.get(
        getCustomerTokenUrl,
        function (data) {
            clientToken = data;
            $('#collapseOne').collapse(true);
            stepZeroSubmitButton.disabled = true;
            let apmAmount = $('#apm-amount').val();
            if (clientToken) {
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
                    }, function (err, dataCollectorInstance) {
                        deviceData = JSON.parse(dataCollectorInstance.deviceData);
                    });
                    braintree.paypalCheckout.create({
                        autoSetDataUserIdToken: true,
                        client: clientInstance
                    }, function (paypalCheckoutErr, paypalCheckoutInstance) {
                        if (paypalCheckoutErr) {
                            console.error('Error creating PayPal Checkout:', paypalCheckoutErr);
                            return;
                        }
                        paypalCheckoutInstance.loadPayPalSDK({
                            vault: true,
                            currency: 'EUR',
                            intent: 'capture',
                            dataAttributes: {
                                amount: apmAmount
                            }
                        }, function () {
                            let apmAmountObject = {'amount': apmAmount};
                            let paypalOrderObject = {
                                flow: 'vault',
                                currency: 'EUR',
                                intent: 'capture'
                            };
                            let finalPayPalObject = {...apmAmountObject, ...paypalOrderObject}
                            paypal.Buttons({
                                fundingSource: paypal.FUNDING.PAYPAL,
                                createOrder: function () {
                                    return paypalCheckoutInstance.createPayment(finalPayPalObject);
                                },
                                onApprove: function (data) {
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
                            }).render('#paypal-button-container');
                        });
                    });
                });
            }
        }
    );
});

$.get(
    getCustomerUrl,
    function (data) {
        document.getElementById('search-customer-form').innerHTML = data;
        $('#customer-list-group a').on('click', function () {
            customerId = this.id;
        });
        stepZeroSubmitButton.disabled = false;
    }
);