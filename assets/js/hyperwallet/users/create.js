function start(JSONEditor)
{
    let userCreateJsonEditor = new JSONEditor(
        document.getElementById('user_create_json_editor'),
        {
            limitDragging: true,
            name: 'User Create',
            modes: ['form','code'],
            mainMenuBar: true,
            navigationBar: false,
            statusBar: false,
            search: false,
            history: false
        },
        {
            clientUserId: "default",
            profileType: "INDIVIDUAL",
            firstName: "John",
            lastName: "Smith",
            dateOfBirth: "1980-01-01",
            email: "john@company.com",
            addressLine1: "123 Main Street",
            city: "New York",
            stateProvince: "NY",
            country: "US",
            postalCode: "10016",
        }
    );

    let createUserUrl = document.getElementById('usersCreate').getAttribute('href');
    let createUserButton = document.getElementById('usersCreateEditor');
    let createUserResultDiv = document.getElementById('user_create_result');
    createUserButton.addEventListener('click', function () {
        createUserButton.disabled = true;
        $.post(
            createUserUrl,
            userCreateJsonEditor.get(),
            function (data) {
                createUserResultDiv.innerHTML = data;
            }
        );
    });
}

module.exports = {
    start
};