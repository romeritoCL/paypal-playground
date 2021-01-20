function start(JSONEditor)
{
    let userCreateJsonEditorContainer = document.getElementById('user_create_json_editor');
    let createUserButton = document.getElementById('usersCreateEditor');
    createUserButton.addEventListener('click', function (event) {
        event.preventDefault();
        alert("hola");
    });

    let userCreateJson = {
        clientUserId: "CSK7b8Ffch",
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
        programToken: "prg-83836cdf-2ce2-4696-8bc5-f1b86077238c"
    };

    let userCreateJsonEditor = new JSONEditor(
        userCreateJsonEditorContainer,
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
        userCreateJson
    );
}

module.exports = {
    start
};