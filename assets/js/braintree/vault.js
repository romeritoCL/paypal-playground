import braintreePayments from './payments';
import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let stepZeroSubmitButton = document.querySelector('#step-0-submit');
let jsClientToken = document.querySelector('.js-client-token');
let clientToken = jsClientToken.dataset.clientToken;
let searchCustomerButton = document.getElementById('search-customer');
let createCustomerButton = document.getElementById('create-customer');
let jsGetCustomerUrl = document.querySelector('.js-get-customer-url');
let getCustomerUrl = jsGetCustomerUrl.dataset.getCustomerUrl;
let jsCreateCustomerUrl = document.querySelector('.js-create-customer-url');
let createCustomerUrl = jsCreateCustomerUrl.dataset.createCustomerUrl;

braintreePayments.animatePaymentForm();

stepZeroSubmitButton.addEventListener('click', function () {
    $('#collapseOne').collapse(true);
    stepZeroSubmitButton.disabled = true;
});

searchCustomerButton.addEventListener('click', function () {
    createCustomerButton.disabled = true;
    searchCustomerButton.disabled = true;
    $.get(
        getCustomerUrl,
        function (data) {
            document.getElementById('search-customer-form').innerHTML = data;
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
                //TODO VERIFY CUSTOMER ID IS SET
                if (true) {
                    stepZeroSubmitButton.disabled = false;
                }
            }
        );
    });
})