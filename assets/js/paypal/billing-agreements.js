import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import token from './billing-agreements/token';
import createBillingAgreement from './billing-agreements/create-billing-agreement';
window.JSONEditor = JSONEditor;

let urlParams = new URLSearchParams(window.location.search);
let billingAgreementToken = urlParams.get('ba_token');

if (billingAgreementToken) {
    $("#collapseTwo").addClass('show');
    createBillingAgreement.startEditor(billingAgreementToken);
} else {
    $("#collapseOne").addClass('show');
    token.startEditor();
}
