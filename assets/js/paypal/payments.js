function startPayments(orderCreateJson, PayPalButtonsJson)
{
    let captureUrl = document.getElementById('capture-url').dataset.captureUrl;
    let orderCreateEditorContainer = document.getElementById('create-order-editor');
    let paypalButtonsEditorContainer = document.getElementById('paypal-buttons-editor');

    let paypalButtonsEditor = new JSONEditor(
        paypalButtonsEditorContainer,
        {
            limitDragging: true,
            name: 'ButtonSettings',
            modes: ['form','code'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
            onChange: function () {
                reloadPayPalButtons();
            }
        },
        PayPalButtonsJson
    );
    paypalButtonsEditor.expandAll();

    let orderCreateEditor = new JSONEditor(
        orderCreateEditorContainer,
        {
            limitDragging: true,
            name: 'ButtonSettings',
            modes: ['code','form'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
            onChange: function () {
                reloadPayPalButtons();
            }
        },
        orderCreateJson
    );

    function reloadPayPalButtons()
    {
        $('#paypal-button-container').empty();
        let PayPalButtonsObject = paypalButtonsEditor.get();
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create(orderCreateEditor.get());
            },
            PayPalButtonsObject,
            onShippingChange: function () {
            },
            onApprove: function (data) {
                captureUrl = captureUrl.replace("payment_id_status", data.orderID);
                jQuery.post(
                    captureUrl,
                    null,
                    function (data) {
                        document.getElementById('result-col').innerHTML = data
                    }
                );
            }
        }).render('#paypal-button-container');
    }

    reloadPayPalButtons();
}

module.exports = {
    startPayments
};
