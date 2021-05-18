import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let transferCreateJsonEditor = new JSONEditor(
    document.getElementById('transfer_create_json_editor'),
    {
        limitDragging: true,
        name: 'Transfer Create',
        modes: ['form','code'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false
    },
    {
        clientTransferId: "DyClk0VG",
        destinationAmount: "20.00",
        notes: "this note is visible for the client",
    }
);

let createTransferUrl = document.getElementById('transferCreate').getAttribute('href');
let createTransferButton = document.getElementById('transferCreate');
let createTransferResultDiv = document.getElementById('transfer_create_result');
createTransferButton.addEventListener('click', function () {
    let transferJsonObject = transferCreateJsonEditor.get()
    transferJsonObject.destinationCurrency = $('#balance').val();
    transferJsonObject.destinationToken = $('#transferMethod').val();
    transferJsonObject.sourceToken = $('.js-user-token').data('userToken');
    $.post(
        createTransferUrl,
        transferJsonObject,
        function (data) {
            createTransferResultDiv.innerHTML = data;
            let transferToken = $('#template-result-id').val();
            let transferCommitButton = $('#transferCommit');
            transferCommitButton.removeAttr('disabled');
            transferCommitButton.on('click', function () {
                let transferCommitUrl = transferCommitButton.attr('href');
                transferCommitUrl = transferCommitUrl.replace("replaceTransferToken", transferToken);
                console.log(transferCommitUrl)
                $.get(
                    transferCommitUrl,
                    function (data) {
                        createTransferResultDiv.innerHTML = data;
                    }
                );
            });
        }
    );
});