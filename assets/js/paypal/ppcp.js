import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCaptureUrl = document.getElementById('order-capture-url').dataset.orderCaptureUrl;
let orderCreateUrl = document.getElementById('order-create-url').dataset.orderCreateUrl;
let orderCreateJson = {
    "intent": "CAPTURE",
    "purchase_units": [
        {
            "amount": {
                "currency_code": "GBP",
                "value": "50"
            }
        }
    ]
};

let PayPalButtons = {
    style: {
        layout: 'vertical',
        color: 'blue',
        shape: 'rect',
        label: 'pay'
    }
};

paypalPayments.startPayments(orderCreateJson, PayPalButtons);

if (paypal.HostedFields.isEligible()) {
    let orderId
    // Renders card fields
    paypal.HostedFields.render({
        // Call your server to set up the transaction
        createOrder: function (data, actions) {
            return fetch(orderCreateUrl, {
                method: "post",
                body: JSON.stringify(orderCreateJson)
            })
            .then((res) => res.json())
            .then((orderData) => {
                orderId = orderData.id
                return orderData.id
            })
        },
        styles: {
            '.valid': {
                color: 'green',
            },
            '.invalid': {
                color: 'red',
            },
            'input': {
                'font-size': '16px',
                'font-family': 'sans-serif',
                color: '#3A3A3A',
                transition: 'color 160ms linear',
                '-webkit-transition': 'color 160ms linear',
                '-webkit-font-smoothing': 'antialiased'
            },
            ':focus': {
                color: '#333333'
            }
        },
        fields: {
            number: {
                selector: '#card-number',
                placeholder: '4111 1111 1111 1111',
            },
            cvv: {
                selector: '#cvv',
                placeholder: '123',
            },
            expirationDate: {
                selector: '#expiration-date',
                placeholder: 'MM/YY',
            },
        },
    }).then((cardFields) => {
        document.querySelector('#paypal-hosted-fields').addEventListener('submit', (event) => {
            event.preventDefault();
            cardFields
                .submit({
                    cardholderName: document.getElementById('card-holder-name').value,
                    contingencies: ['SCA_ALWAYS'] //SCA_WHEN_REQUIRED
                })
                .then(function (payload) {
                    console.log(payload)
                    orderCaptureUrl = orderCaptureUrl.replace("payment_id_status", orderId)
                    fetch(orderCaptureUrl, {
                        method: 'post',

                    })
                    .then((response) => {
                        return response.text()
                    })
                    .then((html) => {
                        document.getElementById('result-col').innerHTML = html
                    })
                })
                .catch((err) => {
                    document.getElementById('result-col').innerHTML = "<pre>" + JSON.stringify(err, null ,2) + "</pre>"
                })
        })
    })
} else {
    console.log(paypal.HostedFields.isEligible())
    // Hides card fields if the merchant isn't eligible
    document.querySelector('#paypal-hosted-fields').style = 'display: none'
}