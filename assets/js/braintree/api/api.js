import listCustomer from './vault/list.js';

function addApiButtonEvent(action, starter)
{
    let button = document.getElementById(action);
    button.addEventListener('click', function (event) {
        event.preventDefault();
        let apiFormDiv = document.getElementById('bt-api-action-list');
        let url = button.getAttribute('href');
        $.get(
            url,
            function (data) {
                apiFormDiv.innerHTML = data;
                starter.start();
            }
        );
    });
}

addApiButtonEvent('listCustomer', listCustomer);
