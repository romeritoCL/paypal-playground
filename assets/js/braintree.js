/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import '../css/braintree.css';

import dropin from 'braintree-web-drop-in';

let submitButton = document.querySelector('#submit-button');
let submitButtonTwo = document.querySelector('#submit-button-two');
let stepOneSubmitButton = document.querySelector('#step-1-submit');
let stepTwoSubmitButton = document.querySelector('#step-2-submit');
let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsPayLoadUrl = document.querySelector('.js-payload-url');
let payloadUrl = jsPayLoadUrl.dataset.payloadUrl;
let jsSaleUrl = document.querySelector('.js-sale-url');
let saleUrl = jsSaleUrl.dataset.saleUrl;

dropin.create({
    authorization: clientToken,
    container: '#dropin-container',
    dataCollector: {
        kount: true
    }
}, function (createErr, instance) {
    submitButton.addEventListener('click', function (event) {
        event.stopPropagation();
        submitButton.disabled=true;
        instance.requestPaymentMethod(function (err, payload) {
            if (err) {
                submitButton.disabled = false
                return false;
            }
            $.post(
                payloadUrl,
                payload,
                function (data) {
                    document.getElementById('step-one-result-row').innerHTML = data
            });
            let deviceData = JSON.parse(payload.deviceData);
            document.querySelector('#payment-nonce').value = payload.nonce;
            document.querySelector('#device-info').value = deviceData.correlation_id;
            document.querySelector('#deviceInformation').value = payload.deviceData;
            window.payload_export=payload;
            setTimeout(function () {
                submitButton.disabled = false;
            }, 2000);
            stepOneSubmitButton.disabled = false;
        })
    });
});

stepOneSubmitButton.addEventListener('click', function (event) {
    $('#collapseTwo').collapse('show');
    stepOneSubmitButton.disabled = true;
});

stepTwoSubmitButton.addEventListener('click', function (event) {
    $('#collapseThree').collapse('show');
    stepTwoSubmitButton.disabled = true;
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
            stepTwoSubmitButton.disabled = false;
        });
})


