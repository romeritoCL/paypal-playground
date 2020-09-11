/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import braintree from 'braintree-web';

let submitButtonOne = document.querySelector('#submit-button-one');
let submitButtonTwo = document.querySelector('#submit-button-two');
let submitButtonThree = document.querySelector('#submit-button-three');
let submitButtonThreeStatus = document.querySelector('#submit-button-three-status');
let stepOneSubmitButton = document.querySelector('#step-1-submit');
let stepTwoSubmitButton = document.querySelector('#step-2-submit');
let stepThreeSubmitButton = document.querySelector('#step-3-submit');
let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsPayLoadUrl = document.querySelector('.js-payload-url');
let payloadUrl = jsPayLoadUrl.dataset.payloadUrl;
let jsSaleUrl = document.querySelector('.js-sale-url');
let saleUrl = jsSaleUrl.dataset.saleUrl;
let jsCaptureUrl = document.querySelector('.js-capture-url');
let captureUrl = jsCaptureUrl.dataset.captureUrl;
let jsGetSaleUrl = document.querySelector('.js-get-sale-url');
let getSaleUrl = jsGetSaleUrl.dataset.getSaleUrl;
let hostedFieldsForm = document.querySelector('#hosted-fields-form');
let deviceData;

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
        submitButtonOne.removeAttribute('disabled');
        hostedFieldsForm.addEventListener('submit', function (event) {
            event.preventDefault();
            hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                if (tokenizeErr) {
                    console.error(tokenizeErr);
                    return;
                }
                $.post(
                    payloadUrl,
                    payload,
                    function (data) {
                        document.getElementById('step-one-result-row').innerHTML = data
                    }
                );
                document.querySelector('#payment-nonce').value = payload.nonce;
                document.querySelector('#device-info').value = deviceData.correlation_id;
                document.querySelector('#deviceInformation').value = payload.deviceData;
                window.payload_export=payload;
                setTimeout(function () {
                    submitButtonOne.disabled = false;
                }, 2000);
                stepOneSubmitButton.disabled = false;
            });
        }, false);
    });
});

submitButtonTwo.addEventListener('click', function () {
    submitButtonTwo.disabled = true;
    let saleDetails = {
        'payment_nonce'         : $('#payment-nonce').val(),
        'correlation_id'        : $('#device-info').val(),
        'amount'                : $('#amount').val(),
        'device_data'           : $('#deviceInformation').val()
    };

    $.post(
        saleUrl,
        saleDetails,
        function (data) {
            document.getElementById('step-two-result-row').innerHTML = data;
            document.getElementById('transaction_id').value = document.getElementById('template-result-id').value
            stepTwoSubmitButton.disabled = false;
        }
    );
})

submitButtonThree.addEventListener('click', function () {
    submitButtonThree.disabled = true;
    let transactionId = $('#transaction_id').val()
    captureUrl = captureUrl.replace("transaction_id_replace", transactionId);
    let captureDetails = {
        'amount'                : $('#amount_capture').val(),
    };

    $.post(
        captureUrl,
        captureDetails,
        function (data) {
            document.getElementById('step-three-result-row').innerHTML = data;
            stepThreeSubmitButton.disabled = false;
        }
    );
})

submitButtonThreeStatus.addEventListener('click', function () {
    submitButtonThreeStatus.disabled = true;
    let transactionId = $('#transaction_id').val()
    getSaleUrl = getSaleUrl.replace("transaction_id_replace", transactionId);
    $.get(
        getSaleUrl,
        function (data) {
            document.getElementById('step-three-result-row').innerHTML = data;
            submitButtonThreeStatus.disabled = false;
        }
    );
})

stepOneSubmitButton.addEventListener('click', function () {
    $('#collapseTwo').collapse('show');
    stepOneSubmitButton.disabled = true;
});

stepTwoSubmitButton.addEventListener('click', function () {
    $('#collapseThree').collapse('show');
    stepTwoSubmitButton.disabled = true;
});

stepThreeSubmitButton.addEventListener('click', function () {
    location.reload();
});
