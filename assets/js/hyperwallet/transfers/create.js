function start(JSONEditor)
{
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
            destinationCurrency: "USD",
            notes: "this note is visible for the client",
            sourceToken: "userToken",
            destinationToken: "transferMethodToken"
        }
    );

    let createTransferUrl = document.getElementById('transfersCreate').getAttribute('href');
    let createTransferButton = document.getElementById('transferCreateEditor');
    let createTransferResultDiv = document.getElementById('transfer_create_result');
    createTransferButton.addEventListener('click', function () {
        $.post(
            createTransferUrl,
            transferCreateJsonEditor.get(),
            function (data) {
                createTransferResultDiv.innerHTML = data;
            }
        );
    });
}

module.exports = {
    start
};