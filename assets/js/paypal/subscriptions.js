paypal.Buttons({
    style: {
        shape: 'pill',
        color: 'blue',
        layout: 'vertical',
        label: 'subscribe'
    },
    createSubscription: function (data, actions) {
        return actions.subscription.create({
            'plan_id': 'P-78J08349NW6750428MORAHLA'
        });
    },
    onApprove: function (data) {
        alert(data.subscriptionID);
    }
}).render('#paypal-button-container');
