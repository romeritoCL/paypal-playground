import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import paypalPayments from './payments';
window.JSONEditor = JSONEditor;

let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let orderCreateJson = {
    intent: 'capture',
    payer: {
        name: {
            given_name: settings['settings-customer-name'],
            surname: settings['settings-customer-family-name'],
        },
        email_address: settings['settings-customer-email'],
        address: {
            address_line_1: settings['settings-customer-street'],
            country_code: settings['settings-customer-country'],
            admin_area_1: settings['settings-customer-province'],
            admin_area_2: settings['settings-customer-city'],
            postal_code: settings['settings-customer-zip-code']
        }
    },
    purchase_units: [{
        reference_id: settings['settings-item-sku'],
        amount: {
            currency_code: settings['settings-customer-currency'],
            value: (+settings['settings-item-price'] + +settings['settings-item-shipping']),
            breakdown: {
                item_total: {
                    value: settings['settings-item-price'],
                    currency_code: settings['settings-customer-currency']
                },
                shipping: {
                    value: (+settings['settings-item-shipping'] * 2),
                    currency_code: settings['settings-customer-currency']
                },
                shipping_discount: {
                    value: settings['settings-item-shipping'],
                    currency_code: settings['settings-customer-currency']
                }
            }
        },
        description: settings['settings-item-description'],
        items: [{
            name: settings['settings-item-name'],
            unit_amount: { currency_code: settings['settings-customer-currency'], value: settings['settings-item-price']},
            quantity: 1,
            sku: settings['settings-item-sku'],
            description: settings['settings-item-description']
        }],
    }]
};

let paypalButtonsStyle = {
    layout: 'vertical',
    color: 'gold',
    shape: 'pill',
    label: 'pay'
};

paypalPayments.startPayments(orderCreateJson, paypalButtonsStyle);

if (paypal.HostedFields.isEligible()) {
    let orderId
    // Renders card fields
    paypal.HostedFields.render({
        // Call your server to set up the transaction
        createOrder: function (data, actions) {
            return actions.order.create(orderCreateEditor.get());
        },
        styles: {
            '.valid': {
                color: 'green',
            },
            '.invalid': {
                color: 'red',
            },
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
            event.preventDefault()
            cardFields
                .submit({
                    cardholderName: document.getElementById('card-holder-name').value
                })
                .then(() => {
                    fetch(`/api/orders/${orderId}/capture`, {
                        method: 'post',
                    })
                        .then((res) => res.json())
                        .then((orderData) => {
                            // Two cases to handle:
                            //   (1) Other non-recoverable errors -> Show a failure message
                            //   (2) Successful transaction -> Show confirmation or thank you
                            // This example reads a v2/checkout/orders capture response, propagated from the server
                            // You could use a different API or structure for your 'orderData'
                            const errorDetail =
                                Array.isArray(orderData.details) && orderData.details[0]
                            if (errorDetail) {
                                var msg = 'Sorry, your transaction could not be processed.'
                                if (errorDetail.description)
                                    msg += '\n\n' + errorDetail.description
                                if (orderData.debug_id)
                                    msg += ' (' + orderData.debug_id + ')'
                                return alert(msg) // Show a failure message
                            }
                            // Show a success message or redirect
                            alert('Transaction completed!')
                        })
                })
                .catch((err) => {
                    alert('Payment could not be captured! ' + JSON.stringify(err))
                })
        })
    })
} else {
    console.log(paypal.HostedFields.isEligible())
    // Hides card fields if the merchant isn't eligible
    document.querySelector('#paypal-hosted-fields').style = 'display: none'
}