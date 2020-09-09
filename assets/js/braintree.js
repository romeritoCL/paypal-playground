/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import '../css/braintree.css';

import dropin from 'braintree-web-drop-in';

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

dropin.create({
    authorization: clientToken,
    container: '#dropin-container',
    dataCollector: {
        kount: true
    }
}, function (createErr, instance) {
    submitButtonOne.addEventListener('click', function (event) {
        event.stopPropagation();
        submitButtonOne.disabled=true;
        instance.requestPaymentMethod(function (err, payload) {
            if (err) {
                submitButtonOne.disabled = false
                return false;
            }
            $.post(
                payloadUrl,
                payload,
                function (data) {
                    document.getElementById('step-one-result-row').innerHTML = data
                }
            );
            let deviceData = JSON.parse(payload.deviceData);
            document.querySelector('#payment-nonce').value = payload.nonce;
            document.querySelector('#device-info').value = deviceData.correlation_id;
            document.querySelector('#deviceInformation').value = payload.deviceData;
            window.payload_export=payload;
            setTimeout(function () {
                submitButtonOne.disabled = false;
            }, 2000);
            stepOneSubmitButton.disabled = false;
        })
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
