import referenceTransaction from "./reference-transaction";

let createBillingAgreement = {};
let createBillingAgreementSubmit= document.getElementById('create-billing-agreement-submit');
let nextStep= document.getElementById('step-2-submit');

createBillingAgreement.showResult = function (resultJson) {
    let createBillingAgreementResultEditorContainer = document.getElementById('create-billing-agreement-result');
    this.jsonEditorResult = new JSONEditor(
        createBillingAgreementResultEditorContainer,
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
    createBillingAgreement.billingAgreementId = resultJson.id;
}

createBillingAgreement.startEditor = function (billingAgreementToken) {
    let createBillingAgreementEditorContainer = document.getElementById('create-billing-agreement-editor');
    this.jsonEditor = new JSONEditor(
        createBillingAgreementEditorContainer,
        {
            limitDragging: true,
            name: 'Billing Agreement',
            mode: 'view',
            mainMenuBar: false,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false,
        },
        {"token_id": billingAgreementToken}
    );
}

createBillingAgreementSubmit.addEventListener('click', function () {
    this.disabled = true;
    let billingAgreementCreateUrl = document.getElementById('billing-agreement-create-url').dataset.billingAgreementCreateUrl;
    $.ajax({
        type: "POST",
        url: billingAgreementCreateUrl,
        data: JSON.stringify(createBillingAgreement.jsonEditor.get()),
        dataType: "json",
        success:  function (data) {
            createBillingAgreement.showResult(JSON.parse(data));
            document.getElementById('step-2-submit').disabled = false;
        }
    });
});

nextStep.addEventListener('click', function () {
    this.disabled = true;
    $("#collapseTwo").removeClass('show');
    $("#collapseThree").addClass('show');
    referenceTransaction.startEditor(createBillingAgreement.billingAgreementId);
});

export default createBillingAgreement;