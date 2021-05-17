import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import usersCreate from './users/create.js';

let createUserbutton = document.getElementById('usersCreate');
createUserbutton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = createUserbutton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            usersCreate.start(JSONEditor);
        }
    );
});

let searchButton = document.getElementById('usersSearch');
searchButton.addEventListener('click', function (event) {
    event.preventDefault();
    let apiFormDiv = document.getElementById('hw-api-action');
    let url = searchButton.getAttribute('href');
    $.get(
        url,
        function (data) {
            apiFormDiv.innerHTML = data;
            $('.user-transfer-methods-button').each(function () {
                $(this).click(function (event) {
                    event.preventDefault();
                    let getTransferMethodsUrl = $(this).attr('href');
                    $.get(
                        getTransferMethodsUrl,
                        function (data) {
                            document.getElementById('transfer-methods-result').innerHTML = data;
                        }
                    );
                });
            });
        }
    );
});