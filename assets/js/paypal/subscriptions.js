paypal.Buttons({
    style: {
        shape: 'pill',
        color: 'blue',
        layout: 'vertical',
        label: 'subscribe'
    },
    createSubscription: function (data, actions) {
        return actions.subscription.create({
            'plan_id': 'P-0D4765787C8312900L5ITVFA'
        });
    },
    onApprove: function (data) {
        alert(data.subscriptionID);
    }
}).render('#paypal-button-container');
