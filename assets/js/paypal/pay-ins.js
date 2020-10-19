let price = document.getElementById("price").value;
let shipping = document.getElementById("shipping").value;
let name = document.getElementById("name").value;
let sku = document.getElementById("sku").value;
let item_description = document.getElementById("item_description").value;
let purchase_description = document.getElementById("purchase_description").value;
let reloadButton = document.getElementById('reload-payment-buttons');
let paymentSentence = document.getElementById('current-payment-sentence');
let buttonContainer = document.getElementById('paypal-button-container');
let settings = JSON.parse(document.getElementById('customer-settings').dataset.settings);
let captureUrl = document.getElementById('capture-url').dataset.captureUrl;

reloadButton.addEventListener('click', function () {
    paymentSentence.innerHTML =
        "You are now buying and incredible <strong>" + name +
        "</strong> for the amazing price of <strong>" + (+price + +shipping) +
        settings['settings-customer-currency'] + "</strong>."
    buttonContainer.innerHTML = "";
    reloadPayPalButtons();
});

function reloadPayPalButtons()
{
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
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
                        value: (+price + +shipping),
                        breakdown: {
                            item_total: {
                                value: price,
                                currency_code: settings['settings-customer-currency']
                            },
                            shipping: {
                                value: (+shipping * 2),
                                currency_code: settings['settings-customer-currency']
                            },
                            shipping_discount: {
                                value: shipping,
                                currency_code: settings['settings-customer-currency']
                            }
                        }
                    },
                    description: purchase_description,
                    items: [{
                        name: name,
                        unit_amount: { currency_code: settings['settings-customer-currency'], value: price},
                        quantity: 1,
                        sku: sku,
                        description: item_description
                    }],
                }],
                application_context: {
                    brand_name: settings['settings-merchant-name'],
                    user_action: "PAY_NOW"
                }
            });
        },
        onApprove: function (data) {
            captureUrl = captureUrl.replace("payment_id_status", data.orderID);
            jQuery.post(
                captureUrl,
                null,
                function (data) {
                    document.getElementById('result-col').innerHTML = data
                }
            );
        },
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'pill',
            label: 'pay'
        },
    }).render('#paypal-button-container');
}

reloadPayPalButtons();