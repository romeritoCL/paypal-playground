function start()
{
    $('.btn-customer-get').each(function () {
        $(this).click(function (event) {
            event.preventDefault();
            let getCustomerUrl = $(this).attr('href');
            $.get(
                getCustomerUrl,
                function (data) {
                    document.getElementById('bt-api-action-get').innerHTML = data;
                }
            );
        });
    });
}

module.exports = {
    start,
};