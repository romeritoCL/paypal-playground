import dropin from 'braintree-web-drop-in';
import braintreePayments from './payments';

let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;

braintreePayments.animatePaymentForm();

dropin.create({
    authorization: clientToken,
    container: '#dropin-container',
    dataCollector: {
        kount: true
    }
}, function (createErr, instance) {
    let submitButtonOne = document.querySelector('#submit-button-one');
    submitButtonOne.addEventListener('click', function (event) {
        event.stopPropagation();
        submitButtonOne.disabled=true;
        instance.requestPaymentMethod(function (err, payload) {
            if (err) {
                submitButtonOne.disabled = false
                return false;
            }
            let deviceData = JSON.parse(payload.deviceData);
            braintreePayments.sendServerPayLoad(payload,deviceData);
        })
    });
});
