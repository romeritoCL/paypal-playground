function editUser(userToken)
{
    $(this).disabled = true;
    let readUrl = $('.js-user-read-url').data('userReadUrl');
    readUrl = readUrl.replace("user_token_replace", userToken);
    $.get(
        readUrl,
        function (data) {
            document.getElementById('searchContainer').innerHTML = JSON.stringify(data);
        }
    );
}

function goToTransferMethods(userToken)
{
    alert('Will happen in the future, give me time');
}

function start(JSONEditor)
{
    $('.btn-user-edit').click(function () {
        editUser($(this).data('user-token'))
    });
    $('.btn-user-goto-transfer').click(function () {
        goToTransferMethods($(this).data('user-token'))
    });
}

module.exports = {
    start,
};