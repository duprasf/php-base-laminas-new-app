// when ready (defined in /application/js/basescript.js) execute init
ready(init);

function init() {
    // when the JWT expires, the user will trigger the
    // 'jwt-expired' event on the body. When it expires
    // this 'app' should logout the user and refresh the content
    document.body.addEventListener("jwt-expired", (e) => {
        logout();
        switchContent();
    });
    // initialize the user
    laminas.user = new User();

    // switch between login and "you are logged in" message
    switchContent();

    // handle the login forms
    let form = document.querySelector('#dbForm');
    if(form) {
        form.addEventListener('submit', loginDb);
    }

    // handle the login forms
    form = document.querySelector('#ldapForm');
    if(form) {
        form.addEventListener('submit', loginLdap);
    }
}

/**
* When trying to login using the DB
*
* @param e
*/
function loginDb(e) {
    e.preventDefault();

    const url = '/en/my-app-with-user/api/v1/user';

    let data = {
        'email':document.querySelector('#emailDB').value,
        'password':document.querySelector('#passwordDB').value,
        'remember':!!document.querySelector('#rememberDb:checked')
    };
    login(url, data);
}

/**
* When trying to login using LDAP
*
* @param e
*/
function loginLdap(e) {
    e.preventDefault();

    const url = '/en/my-app-with-user/api/v1/user-ldap';

    let data = {
        'email':document.querySelector('#emailLdap').value,
        'password':document.querySelector('#passwordLdap').value,
        'remember':!!document.querySelector('#rememberLdap:checked')
    };
    login(url, data);
}

/**
* Both functions to login using DB or LDAP calls this function
* This will use the fetch API to post the username/password to the server
*
* @param url
* @param data
*/
function login(url, data) {
    fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                // do something when there is an error with credentials...
                alert(data.error);
                // make sure user is not logged in anymore (just in case)
                laminas.user.logout();
                return;
            }
            laminas.user.handleLogin(data.jwt, data.remember);
            switchContent();
        })
        .catch( error => {
            alert(error);
        }
    );
}

/**
* This will update the content in the panel between login form
* and "you are logged in"/logout button
*
*/
function switchContent() {
    refreshApiContent();
    let contentDb = document.querySelector('#dbContent');
    let contentLdap = document.querySelector('#ldapContent');

    if(laminas.user.isLoggedIn()) {
        let username = sprintf(strings['you are logged in'], laminas.user.email??laminas.user.userId??laminas.user.id??'user not found')

        contentDb.innerHTML = (laminas.user.type == 'db' ? username : 'logged in LDAP')
            + '<br><button class="btn btn-default" id="logoutDb">'
            + strings['logout']
            + '</button>'
        ;
        contentLdap.innerHTML = (laminas.user.type == 'ldap' ? username : 'logged by DB')
            + '<br><button class="btn btn-default" id="logoutLdap">'
            + strings['logout']
            + '</button>'
        ;

        contentDb.querySelector('#logoutDb').addEventListener('click', logout);
        contentLdap.querySelector('#logoutLdap').addEventListener('click', logout);

        document.body.classList.add('isLoggedIn');
        return;
    }

    // Adds the .isLoggedIn class to the body which is used to switch the image
    document.body.classList.remove('isLoggedIn');

    contentDb.innerHTML =
        '<p>'+strings['You can use any email and the password is "test"']+'</p>'
        +'<form method="get" action="#" id="dbForm">'
        +'<div class="form-group">'
        +'<label for="emailDB" class="required"><span class="field-name">'+strings['Email address']+'</span> <strong class="required">('+strings['required']+')</strong></label>'
        +'<input type="email" class="form-control" id="emailDB" placeholder="'+strings['Email address']+'">'
        +'</div><div class="form-group">'
        +'<label for="passwordDB" class="required"><span class="field-name">'+strings['Password']+'</span> <strong class="required">('+strings['required']+')</strong></label>'
        +'<input type="password" class="form-control" id="passwordDB"></div>'
        +'<div class="checkbox"><label><input type="checkbox" name="remember" value="1" id="rememberDb">&nbsp;'+strings['Remember me']+'</label></div>'
        +'<button type="submit" class="btn btn-default">'+strings['Submit']+'</button></form>'
    ;
    contentLdap.innerHTML =
        '<p>'+strings['Use your your Windows username (or email)/password']+'</p>'
        +'<form method="get" action="#" id="ldapForm">'
        +'<div class="form-group">'
        +'<label for="emailLdap" class="required"><span class="field-name">'+strings['Email address']+'</span> <strong class="required">('+strings['required']+')</strong></label>'
        +'<input type="email" class="form-control" id="emailLdap" placeholder="'+strings['Email address']+'">'
        +'</div><div class="form-group">'
        +'<label for="passwordLdap" class="required"><span class="field-name">'+strings['Password']+'</span> <strong class="required">('+strings['required']+')</strong></label>'
        +'<input type="password" class="form-control" id="passwordLdap"></div>'
        +'<div class="checkbox"><label><input type="checkbox" name="remember" value="1" id="rememberLdap">&nbsp;'+strings['Remember me']+'</label></div>'
        +'<button type="submit" class="btn btn-default">'+strings['Submit']+'</button></form>'
    ;
    let form = document.querySelector('#dbForm');
    if(form) {
        form.addEventListener('submit', loginDb);
    }

    form = document.querySelector('#ldapForm');
    if(form) {
        form.addEventListener('submit', loginLdap);
    }
}

/**
* This 'app' logout function will call the user.logout (which delete the JWT)
* and will call switchContent to update the form so the user can log in again
*
*/
function logout() {
    laminas.user.logout();
    switchContent();
}

/**
* Ask the API to provide the content based on login status
*
*/
function refreshApiContent() {
    fetch('/en/my-app-with-user/api/v1/content', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Access-Token': laminas.user.getJwt()
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                // do something when there is an error with credentials...
                alert(data.error);
                return;
            }
            handleRefreshApiContent(data.data);
        })
        .catch( error => {
            alert('ERROR: '+error);
        }
    );
}

/**
* Update the content of the 4th panel with content depending on login status
*
*/
function handleRefreshApiContent(data) {
    let container = document.querySelector('#content-list');
    while (container.firstChild) {
        container.removeChild(container.lastChild);
    }

    for(let i of data) {
        let li = document.createElement('li');
        li.innerHTML = i.name;
        li.classList.add(i.category);
        container.appendChild(li);
    }
}
