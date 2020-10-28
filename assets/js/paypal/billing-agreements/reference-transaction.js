import deleteBillingAgreement from "./delete-billing-agreement";

let referenceTransaction = {};
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let referenceTransactionSubmit = document.getElementById('reference-transaction-submit');
let nextStep= document.getElementById('step-3-submit');

referenceTransaction.getReferenceTransactionBody = function (billingAgreementId) {
    return {
        payer: {
            payment_method: "PAYPAL",
            funding_instruments: [{
                billing: {
                    billing_agreement_id: billingAgreementId
                }
            }]
        },
        intent: 'sale',
        transactions: [{
            amount: {
                currency: settings['settings-customer-currency'],
                total: (+settings['settings-item-price'] + +settings['settings-item-shipping'])
            },
            description: settings['settings-item-description'],
        }]
    };
};

referenceTransaction.showResult = function (resultJson) {
    let referenceTransactionResultEditorContainer = document.getElementById('reference-transaction-result');
    referenceTransactionResultEditorContainer.innerHTML = "";
    this.jsonEditorResult = new JSONEditor(
        referenceTransactionResultEditorContainer,
        {
            limitDragging: true,
            name: 'Result',
            mode: 'view',
            mainMenuBar: false,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
        },
        resultJson
    );
    this.jsonEditorResult.expandAll();
}

referenceTransaction.startEditor = function (billingAgreementId) {
    this.billingAgreementId = billingAgreementId;
    let createReferenceTransactionEditorContainer = document.getElementById('reference-transaction-editor');
    this.jsonEditor = new JSONEditor(
        createReferenceTransactionEditorContainer,
        {
            limitDragging: true,
            name: 'Reference Transaction',
            modes: ['code'],
            mainMenuBar: false,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
        },
        this.getReferenceTransactionBody(billingAgreementId)
    );
}

referenceTransactionSubmit.addEventListener('click', function () {
    this.disabled = true;
    let referenceTransactionCreateUrl = document.getElementById('reference-transaction-url').dataset.referenceTransactionUrl;
    $.ajax({
        type: "POST",
        url: referenceTransactionCreateUrl,
        data: JSON.stringify(referenceTransaction.jsonEditor.get()),
        dataType: "json",
        success:  function (data) {
            referenceTransaction.showResult(JSON.parse(data));
            document.getElementById('step-3-submit').disabled = false;
            setTimeout(function () {
                referenceTransactionSubmit.disabled = false },2000);
        }
    });
});

nextStep.addEventListener('click', function () {
    this.disabled = true;
    $("#collapseThree").removeClass('show');
    $("#collapseFour").addClass('show');
    deleteBillingAgreement.deleteBillingAgreement(referenceTransaction.billingAgreementId)
});

export default referenceTransaction;