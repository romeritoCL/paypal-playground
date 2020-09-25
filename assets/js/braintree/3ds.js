import dropin from 'braintree-web-drop-in';
import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let stepZeroSubmitButton = document.querySelector('#step-0-submit');
stepZeroSubmitButton.disabled = false;
let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let customerJsonContainer = document.getElementById('customer-json-editor');
let customerJson = {
    amount: '180',
    email: 'johndoe@paypal.com',
    billingAddress: {
        givenName: 'John',
        surname: 'Doe',
        phoneNumber: '600123123',
        streetAddress: 'Pablo Ruiz Picaso, 1',
        extendedAddress: '11 floor',
        locality: 'Madrid',
        region: 'ES',
        postalCode: '28031',
        countryCodeAlpha2: 'ES'
    },
    additionalInformation: {
        workPhoneNumber: '8101234567',
        shippingGivenName: 'John',
        shippingSurname: 'Doe',
        shippingPhone: '8101234567',
        shippingAddress: {
            streetAddress: 'Pablo Ruiz Picaso, 1',
            extendedAddress: '11 floor',
            locality: 'Madrid',
            region: 'ES',
            postalCode: '28031',
            countryCodeAlpha2: 'ES'
        }
    },
};
let customerJsonEditor = new JSONEditor(
    customerJsonContainer,
    {
        limitDragging: true,
        name: 'CustomerInfo',
        modes: ['form','code'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false
    },
    customerJson
);
customerJsonEditor.expandAll();
braintreePayments.animatePaymentForm();

dropin.create({
    authorization: clientToken,
    container: '#dropin-container',
    dataCollector: {
        kount: true
    },
    threeDSecure: true
}, function (createErr, instance) {
    let submitButtonOne = document.querySelector('#submit-button-one');
    submitButtonOne.addEventListener('click', function (event) {
        event.stopPropagation();
        submitButtonOne.disabled = true;
        instance.requestPaymentMethod(
            {
                threeDSecure: customerJsonEditor.get()
            },
            function (err, payload) {
                if (err) {
                    submitButtonOne.disabled = false
                    return false;
                }
                let deviceData = JSON.parse(payload.deviceData);
                braintreePayments.sendServerPayLoad(payload, deviceData);
            }
        );
    });
});

stepZeroSubmitButton.addEventListener('click', function () {
    $('#collapseOne').collapse(true);
    stepZeroSubmitButton.disabled = true;
});