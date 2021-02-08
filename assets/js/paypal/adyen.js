import AdyenCheckout from '@adyen/adyen-web';
import '@adyen/adyen-web/dist/adyen.css';

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let paymentMethodsResponse = JSON.parse(document.getElementById('payment-methods').dataset.paymentMethods);
let clientKey = JSON.parse(document.getElementById('client-key').dataset.clientKey);
let paypalId = JSON.parse(document.getElementById('paypal-id').dataset.paypalId);

const configuration = {
    locale: settings['settings-customer-locale'],
    environment: "test",
    clientKey: clientKey,
    paymentMethodsResponse: paymentMethodsResponse, // The payment methods response returned in step 1.
    onChange: handleOnChange, // Your function for handling onChange event
    onAdditionalDetails: handleOnAdditionalDetails // Your function for handling onAdditionalDetails event
};

const checkout = new AdyenCheckout(configuration);

function handleOnChange(state, component)
{}

function handleOnAdditionalDetails(state, component)
{}

function makePayment(data)
{
    let serverPayload = {
        paymentMethod: data.paymentMethod,
        amount: {
            currency: settings['settings-customer-currency'],
            value: 100 * settings['settings-item-price']
        }
    };
    let paymentUrl = document.getElementById('payment-url').dataset.paymentUrl;
    return $.post(
        paymentUrl,
        serverPayload,
    );
}

function submitDetails(data, containerId)
{
    let paymentDetailsUrl = document.getElementById('payment-details-url').dataset.paymentDetailsUrl;
    return $.post(
        paymentDetailsUrl,
        data,
        function (data) {
            document.getElementById(containerId).innerHTML = data
        }
    );
}

function startComponents()
{
    window.component = checkout.create("paypal", {
        environment: "test",
        countryCode: settings['settings-customer-country'],
        amount: {
            currency: settings['settings-customer-currency'],
            value: 100 * settings['settings-item-price']
        },
        style: {
            color: "gold",
            shape: "pill",
            label: "checkout"
        },
        intent: "capture",
        merchantId: paypalId,
        onSubmit: (state, component) => {
            // Your function calling your server to make the /payments request.
            makePayment(state.data)
                .then(response => {
                    if (response.action) {
                        component.handleAction(response.action);
                    } else {
                        console.log(response);
                    }
                })
                .catch(error => {
                    throw Error(error);
                });
        },
        onCancel: (data, component) => {
            component.setStatus('ready');
        },
        onError: (error, component) => {
            component.setStatus('ready');
        },
        onAdditionalDetails: (state, component) => {
            submitDetails(state.data, 'payment-details')
        }
    }).mount("#paypal-container");
}

function startDropin()
{
    window.dropin = checkout.create('dropin', {
        openFirstPaymentMethod: false,
        paymentMethodsConfiguration: {
            paypal: {
                merchantId: paypalId,
                environment: "test",
                countryCode: settings['settings-customer-country'],
                amount: {
                    currency: settings['settings-customer-currency'],
                    value: 100 * settings['settings-item-price']
                },
                intent: "capture",
                onCancel: (data, dropin) => {
                    dropin.setStatus('ready');
                }
            }
        },
        onSubmit: (state, dropin) => {
            makePayment(state.data)
                .then(response => {
                    if (response.action) {
                        dropin.handleAction(response.action);
                    } else {
                        console.log(response);
                    }
                })
                .catch(error => {
                    throw Error(error);
                });
        },
        onAdditionalDetails: (state, dropin) => {
            submitDetails(state.data, 'dropin-details')
        },
        onError: (state, dropin) => {
            // Sets your prefered status of Drop-in when an error occurs. In this example, return to the initial state.
            dropin.setStatus('ready');
        },
    }).mount('#paypal-dropin-container');
}

$('#paypal-start-dropin').click(function () {
    $('#paypal-start-components').attr('disabled', true);
    this.disabled = true;
    startDropin()
});
$('#paypal-start-components').click(function () {
    $('#paypal-start-dropin').attr('disabled', true);
    this.disabled = true;
    startComponents()
});
