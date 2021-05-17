import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let userJson = $('#user-json').data('userJson');
let userEditJson = new JSONEditor(
    document.getElementById('user_edit_json_editor'),
    {
        limitDragging: true,
        name: 'User Edit',
        modes: ['form','code'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false
    },
    userJson
);

let editUrl = $('.js-user-edit-url').data('userEditUrl');
editUrl = editUrl.replace("user_token_replace", userJson.programToken);
let editUserButton = document.getElementById('usersEditButton');
let editUserResultDiv = document.getElementById('user_edit_result');
editUserButton.addEventListener('click', function () {
    $.post(
        editUrl,
        userEditJson.get(),
        function (data) {
            editUserResultDiv.innerHTML = data;
        }
    );
});