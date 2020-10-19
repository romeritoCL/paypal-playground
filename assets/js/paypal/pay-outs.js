let sendPayment = document.getElementById('send-money-button');
let refreshPayment = document.getElementById('refresh-status');
let payoutsCreateUrl = document.getElementById('payouts-url').dataset.payoutsUrl;
let payoutsRefreshUrl = document.getElementById('payouts-refresh-url').dataset.payoutsRefreshUrl;
let resultDiv = document.getElementById('result-col');

sendPayment.addEventListener('click',function () {
    sendPayment.disabled = true;
    let formData = $('#payout-form').find("select, input").serialize();
    $.post(
        payoutsCreateUrl,
        formData,
        function (data) {
            resultDiv.innerHTML = data
        }
    );
    setTimeout(function () {
            sendPayment.disabled = false;
    },2000);
});

refreshPayment.addEventListener('click', function () {
    refreshPayment.disabled = true;
    try {
        payoutsRefreshUrl = payoutsRefreshUrl.replace("payout_batch_status", document.getElementById("template-result-id").value);
    } catch (err) {
        alert('Please create payout first')
        setTimeout(function () {
            refreshPayment.disabled = false
        },2000);
        return;
    }
    $.get(
        payoutsRefreshUrl,
        function (data) {
            document.getElementById('result-col').innerHTML = data
        }
    );
    setTimeout(function () {
        refreshPayment.disabled = false;},2000);
});
