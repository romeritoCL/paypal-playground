let createInvoice = document.getElementById('send-invoice')
let refreshInvoice = document.getElementById('refresh-invoice')
let invoiceCreateUrl = document.getElementById('invoice-url').dataset.invoiceUrl;
let invoiceRefreshUrl = document.getElementById('invoice-refresh-url').dataset.invoiceRefreshUrl;
let resultQRCodeDiv = document.getElementById('qr-code-result');
let resultDiv = document.getElementById('qr-code-result');

createInvoice.addEventListener('click', function () {
    createInvoice.disabled = true;
    let formData = $('#invoice_form').find("select, input").serialize();
    $.post(
        invoiceCreateUrl,
        formData,
        function (data) {
            resultQRCodeDiv.innerHTML = data
        }
    );
    setTimeout(function () {
        createInvoice.disabled = false;
    },2000);
});

refreshInvoice.addEventListener('click', function () {
    refreshInvoice.disabled = true;
    try {
        invoiceRefreshUrl = invoiceRefreshUrl.replace("invoice_id", document.getElementById("template-result-id").value);
    } catch (err) {
        alert('Please get create QR code first')
        setTimeout(function () {
            refreshInvoice.disabled = false;
        },2000);
        return;
    }
    $.get(
        invoiceRefreshUrl,
        function (data) {
            resultDiv.innerHTML = data
        }
    );
    setTimeout(function () {
        refreshInvoice.disabled = false;
    },2000);
});
