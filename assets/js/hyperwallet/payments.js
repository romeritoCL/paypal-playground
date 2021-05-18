import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paymentsCreate from './payments/create.js';

let createPaymentButton = document.getElementById('paymentsCreate');
createPaymentButton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = createPaymentButton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            paymentsCreate.start(JSONEditor);
        }
    );
});

let paymentListButton = document.getElementById('paymentsList');
paymentListButton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = paymentListButton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            $('.payment-details-button').each(function () {
                $(this).click(function (event) {
                    event.preventDefault();
                    let getPaymentDetailsUrl = $(this).attr('href');
                    $.get(
                        getPaymentDetailsUrl,
                        function (data) {
                            document.getElementById('payment-details-result').innerHTML = data;
                        }
                    );
                });
            });
        }
    );
});