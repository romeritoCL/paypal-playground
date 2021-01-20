import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import usersCreate from './users/create.js';

function addApiButtonEvent(action, starter)
{
    let button = document.getElementById(action);
    button.addEventListener('click', function (event) {
        event.preventDefault();
        let apiFormDiv = document.getElementById('hw-api-action');
        let url = button.getAttribute('href');
        $.get(
            url,
            function (data) {
                apiFormDiv.innerHTML = data;
                starter.start(JSONEditor);
            }
        );
    });
}

addApiButtonEvent('usersCreate', usersCreate);