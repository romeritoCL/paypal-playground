import dropin from 'braintree-web-drop-in';
import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let stepZeroSubmitButton = document.querySelector('#step-0-submit');
stepZeroSubmitButton.disabled = false;
let jsClientToken = document.querySelector('.js-client-token');
let jsAmount = document.querySelector('.js-amount');
let clientToken = jsClientToken.dataset.clientToken;
let amount = jsAmount.dataset.amount;
let customerJsonContainer = document.getElementById('customer-json-editor');
let customerJson = {
    amount: amount,
    email: 'cmoreno@paypal.com',
    billingAddress: {
        givenName: 'Carlos', // ASCII-printable characters required, else will throw a validation error
        surname: 'Moreno', // ASCII-printable characters required, else will throw a validation error
        phoneNumber: '8101234567',
        streetAddress: '555 Smith St.',
        extendedAddress: '#5',
        locality: 'Oakland',
        region: 'CA',
        postalCode: '12345',
        countryCodeAlpha2: 'US'
    },
    additionalInformation: {
        workPhoneNumber: '8101234567',
        shippingGivenName: 'Jill',
        shippingSurname: 'Doe',
        shippingPhone: '8101234567',
        shippingAddress: {
            streetAddress: '555 Smith St.',
            extendedAddress: '#5',
            locality: 'Oakland',
            region: 'CA',
            postalCode: '12345',
            countryCodeAlpha2: 'US'
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
            { threeDSecure: customerJsonEditor.get() },
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