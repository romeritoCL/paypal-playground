import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import dropin from "braintree-web-drop-in";

let stepZeroSubmitButton = document.querySelector('#step-0-submit');
let searchCustomerButton = document.getElementById('search-customer');
let createCustomerButton = document.getElementById('create-customer');
let jsGetCustomerUrl = document.querySelector('.js-get-customer-url');
let getCustomerUrl = jsGetCustomerUrl.dataset.getCustomerUrl;
let jsGetCustomerTokenUrl = document.querySelector('.js-get-customer-token-url');
let getCustomerTokenUrl = jsGetCustomerTokenUrl.dataset.getCustomerTokenUrl;
let jsCreateCustomerUrl = document.querySelector('.js-create-customer-url');
let createCustomerUrl = jsCreateCustomerUrl.dataset.createCustomerUrl;
let clientToken, customerId;

braintreePayments.animatePaymentForm();

stepZeroSubmitButton.addEventListener('click', function () {
    getCustomerTokenUrl = getCustomerTokenUrl.replace("customer_id_replace", customerId);
    $.get(
        getCustomerTokenUrl,
        function (data) {
            clientToken = data;
            $('#collapseOne').collapse(true);
            stepZeroSubmitButton.disabled = true;
            if (clientToken) {
                dropin.create({
                    vaultManager: true,
                    authorization: clientToken,
                    container: '#dropin-container',
                    dataCollector: {
                        kount: true
                    },
                    paypal: {
                        flow: 'vault',
                        buttonStyle: {
                            color: 'black',
                            shape: 'rect',
                            size: 'responsive',
                            label: 'paypal',
                            tagline: false
                        }
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
            }
        }
    );
});

searchCustomerButton.addEventListener('click', function () {
    createCustomerButton.disabled = true;
    searchCustomerButton.disabled = true;
    $.get(
        getCustomerUrl,
        function (data) {
            document.getElementById('search-customer-form').innerHTML = data;
            $('#customer-list-group a').on('click', function () {
                customerId = this.id;
            });
            stepZeroSubmitButton.disabled = false;
        }
    );
})

createCustomerButton.addEventListener('click', function () {
    createCustomerButton.disabled = true;
    searchCustomerButton.disabled = true;
    document.getElementById('create-customer-form').classList.remove('invisible');
    let customerJsonContainer = document.getElementById('customer-json-editor');
    let defaultCustomer = {
        email: '',
        firstName: '',
        lastName: '',
        company: 'PayPal Incorporated',
        website: 'www.paypal.com',
        phone: '600123123'
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
        defaultCustomer
    );
    customerJsonEditor.expandAll();
    let customerCreateButton = document.getElementById('create-customer-post');

    customerCreateButton.addEventListener('click', function () {
        $.post(
            createCustomerUrl,
            customerJsonEditor.get(),
            function (data) {
                document.getElementById('create-customer-form').innerHTML = data;
                let createCustomerResult = document.getElementById('template-result-id');
                customerId = createCustomerResult.value;
                createCustomerResult.parentNode.removeChild(createCustomerResult);
                if (customerId) {
                    stepZeroSubmitButton.disabled = false;
                }
            }
        );
    });
})
