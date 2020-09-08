/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import '../css/braintree.css';

import dropin from 'braintree-web-drop-in';

let submitButton = document.querySelector('#submit-button');
let stepOneSubmitButton = document.querySelector('#step-1-submit');
let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let jsPayLoadUrl = document.querySelector('.js-payload-url');
let payloadUrl = jsPayLoadUrl.dataset.payloadUrl;

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
            setTimeout(function () {
                submitButton.disabled = false;
            }, 2000);
            stepOneSubmitButton.disabled = false;
        })
    });
});

stepOneSubmitButton.addEventListener('click', function (event) {
    event.stopPropagation();
});


