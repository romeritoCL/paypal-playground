let deleteBillingAgreement = {};

deleteBillingAgreement.showResult = function (resultJson) {
    let deleteBillingAgreementResultEditorContainer = document.getElementById('delete-billing-agreement-result');
    this.jsonEditorResult = new JSONEditor(
        deleteBillingAgreementResultEditorContainer,
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

deleteBillingAgreement.deleteBillingAgreement = function (billingAgreementId) {
    let deleteBillingAgreementSubmit = document.getElementById('delete-billing-agreement-submit');
    deleteBillingAgreementSubmit.addEventListener('click', function () {
        this.disabled = true;
        let deleteBillingAgreementUrl = document.getElementById('delete-billing-agreement-url').dataset.deleteBillingAgreementUrl;
        deleteBillingAgreementUrl = deleteBillingAgreementUrl.replace("billing_agreement_id", billingAgreementId);
        $.ajax({
            type: "DELETE",
            url: deleteBillingAgreementUrl,
            success:  function (data) {
                let jsonResult;
                document.getElementById('step-4-submit').disabled = false;
                if (data === "") {
                    jsonResult = {billingAgreementId: billingAgreementId, status: 'deleted'};
                } else {
                    jsonResult = JSON.parse(data)
                }
                let createBillingAgreementResultEditorContainer = document.getElementById('delete-billing-agreement-success');
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
                    jsonResult
                );
                this.jsonEditorResult.expandAll();
            }
        });
    });
}

let nextStep= document.getElementById('step-4-submit');
nextStep.addEventListener('click', function () {
    this.disabled = true;
    window.location = window.location.pathname
});

export default deleteBillingAgreement;