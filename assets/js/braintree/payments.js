import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

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
    let serverOptionsEditor = document.getElementById('server-options-json-editor');
    let serverOptionsObject = {
        customer: {
            firstName: "Drew",
            lastName: "McAllen",
            company: "DevOrAlive",
        },
    };

    let serverOptionsJsonEditor = new JSONEditor(
        serverOptionsEditor,
        {
            limitDragging: true,
            name: 'Server Options',
            modes: ['code'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false
        },
        serverOptionsObject
    );

    submitButtonTwo.addEventListener('click', function () {
        submitButtonTwo.disabled = true;
        let saleDetails = {
            'payment_nonce'         : $('#payment-nonce').val(),
            'correlation_id'        : $('#device-info').val(),
            'amount'                : $('#amount').val(),
            'device_data'           : $('#deviceInformation').val(),
            'server_options'         : JSON.stringify(serverOptionsJsonEditor.get())
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
    let serverData = {...payload, ...deviceData };
    let jsPayLoadUrl = document.querySelector('.js-payload-url');
    let payloadUrl = jsPayLoadUrl.dataset.payloadUrl;
    let stepOneSubmitButton = document.querySelector('#step-1-submit');
    let submitButtonOne = document.querySelector('#submit-button-one');
    if (submitButtonOne) {
        submitButtonOne.disabled = true;
    }

    $.post(
        payloadUrl,
        serverData,
        function (data) {
            document.getElementById('step-one-result-row').innerHTML = data
        }
    );
    document.querySelector('#payment-nonce').value = payload.nonce;
    document.querySelector('#device-info').value = deviceData.correlation_id;
    document.querySelector('#deviceInformation').value = JSON.stringify(deviceData);
    window.payload_export=payload;
    if (submitButtonOne) {
        setTimeout(function () {
            submitButtonOne.disabled = false;
        }, 2000);
    }
    stepOneSubmitButton.disabled = false;
}

export default {
    sendServerPayLoad,
    animatePaymentForm
};
