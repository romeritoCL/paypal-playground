/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

function animatePaymentForm()
{
    let submitButtonTwo = document.querySelector('#submit-button-two');
    let submitButtonThree = document.querySelector('#submit-button-three');
    let submitButtonThreeStatus = document.querySelector('#submit-button-three-status');
    let stepOneSubmitButton = document.querySelector('#step-1-submit');
    let stepTwoSubmitButton = document.querySelector('#step-2-submit');
    let stepThreeSubmitButton = document.querySelector('#step-3-submit');
    let jsSaleUrl = document.querySelector('.js-sale-url');
    let saleUrl = jsSaleUrl.dataset.saleUrl;
    let jsCaptureUrl = document.querySelector('.js-capture-url');
    let captureUrl = jsCaptureUrl.dataset.captureUrl;
    let jsGetSaleUrl = document.querySelector('.js-get-sale-url');
    let getSaleUrl = jsGetSaleUrl.dataset.getSaleUrl;

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
        $('#collapseTwo').collapse(true);
        stepOneSubmitButton.disabled = true;
    });

    stepTwoSubmitButton.addEventListener('click', function () {
        $('#collapseThree').collapse(true);
        stepTwoSubmitButton.disabled = true;
    });

    stepThreeSubmitButton.addEventListener('click', function () {
        location.reload();
    });
}

function sendServerPayLoad(payload, deviceData)
{
    let jsPayLoadUrl = document.querySelector('.js-payload-url');
    let payloadUrl = jsPayLoadUrl.dataset.payloadUrl;
    let stepOneSubmitButton = document.querySelector('#step-1-submit');
    let submitButtonOne = document.querySelector('#submit-button-one');
    if (submitButtonOne) {
        submitButtonOne.disabled = true;
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
    if (submitButtonOne) {
        setTimeout(function () {
            submitButtonOne.disabled = false;
        }, 2000);
    }
    stepOneSubmitButton.disabled = false;
}

module.exports = {
    sendServerPayLoad,
    animatePaymentForm
};
