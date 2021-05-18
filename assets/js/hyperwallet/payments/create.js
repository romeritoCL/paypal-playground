function start(JSONEditor)
{
    let paymentCreateJsonEditor = new JSONEditor(
        document.getElementById('payment_create_json_editor'),
        {
            limitDragging: true,
            name: 'Payment Create',
            modes: ['form','code'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false
        },
        {
            amount: "20.00",
            currency: "USD",
            clientPaymentId: "DyClk0VG",
            purpose: "OTHER",
            expiresOn: "2021-12-31",
            destinationToken: "userToken",
            notes: "this note is visible for the client"
        }
    );

    let createPaymentUrl = document.getElementById('paymentsCreate').getAttribute('href');
    let createPaymentButton = document.getElementById('paymentCreateEditor');
    let createPaymentResultDiv = document.getElementById('payment_create_result');
    createPaymentButton.addEventListener('click', function () {
        $.post(
            createPaymentUrl,
            paymentCreateJsonEditor.get(),
            function (data) {
                createPaymentResultDiv.innerHTML = data;
            }
        );
    });
}

module.exports = {
    start
};