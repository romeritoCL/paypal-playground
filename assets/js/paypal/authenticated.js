import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';

let customerJsonContainer = document.getElementById('paypal-info-editor');
let paypalCustomerInfo = JSON.parse(document.getElementById('paypal-customer-info').dataset.paypalCustomerInfo);
let getTransactions = document.getElementById('get-transactions');
let getTransactionsUrl = document.getElementById('transactions-url').dataset.transactionsUrl;

new JSONEditor(
    customerJsonContainer,
    {
        limitDragging: true,
        name: 'ButtonSettings',
        modes: ['view'],
        mainMenuBar: true,
        navigationBar: false,
        statusBar: false,
        search: false,
        history: false,
    },
    paypalCustomerInfo
);

getTransactions.addEventListener('click', function () {
        $.get(
            getTransactionsUrl,
            function (data) {
                    document.getElementById('paypal-info-transactions').innerHTML = data
            }
        );
})
