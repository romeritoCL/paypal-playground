import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import transfersCreate from './transfers/create.js';

let createTransferButton = document.getElementById('transfersCreate');
createTransferButton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = createTransferButton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            transfersCreate.start(JSONEditor);
        }
    );
});

let transferListButton = document.getElementById('transfersList');
transferListButton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = transferListButton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            $('.transfer-details-button').each(function () {
                $(this).click(function (event) {
                    event.preventDefault();
                    let getTransferDetailsUrl = $(this).attr('href');
                    $.get(
                        getTransferDetailsUrl,
                        function (data) {
                            document.getElementById('transfer-details-result').innerHTML = data;
                        }
                    );
                });
            });
        }
    );
});