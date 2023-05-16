/**
* A class for the User
*/
class User {
    /**
    * return true if logged in and false if not logged in or if the JWT expired
    */
    get isLoggedIn() {
        if(!this.getJwt()) {
            return false;
        }
        let payload = this.getJwtPayload();
        return payload.exp > (Date.now()/1000);
    }
    get userId() {
        let payload = this.getJwtPayload();
        return payload.id;
    }

    constructor() {
        let payload = this.getJwtPayload();
        if(!payload) {
            return;
        }
        for(var key in payload) {
            if(!this[key]) {
                this[key] = payload[key];
            }
        }
        // Set a time out for when the JWT expires.
        // The default behavior does not logout the user automatically in case the
        // app wants to do something before end. It would be easy to set a call
        // a few minutes before to alert the user to extends the session for example.
        let time = (payload.exp*1000)-Date.now();
        if(time < 100) {
            // a minimum of 100 ms should be used for the call back
            // if the token is expired
            time = 100;
        }
        this.jwtTimeout = setTimeout(this.jwtExpired.bind(this), time);
        // (the same timeout is set when a user is logged in and timeout ends on logout)
    }

    handleLogin(jwt, remember) {
        this.saveJwt(jwt, remember);

        let payload = this.getJwtPayload();
        for(var key in payload) {
            if(!this[key]) {
                this[key] = payload[key];
            }
        }
    }

    /**
    * Let the app know that the JWT has expired and should take the
    * appropriate actions like login out the user
    */
    jwtExpired(){
        clearTimeout(this.jwtTimeout);
        const event = new CustomEvent("jwt-expired", {
            bubbles: true,
            detail: { },
        });
        document.body.dispatchEvent(event);
    }

    logout() {
        clearTimeout(this.jwtTimeout);
        sessionStorage.removeItem('jwt');
        localStorage.removeItem('jwt');
        this.jwtPayload=null;
    }

    saveJwt(jwt, remember) {
        if(remember == undefined) {
            remember = localStorage.getItem('jwt') ? true : false;
        }

        sessionStorage.removeItem('jwt');
        localStorage.removeItem('jwt');
        if(!remember) {
            // if not "remember" save in session until browser stops
            sessionStorage.setItem('jwt', jwt);
        } else {
            // if clicked to remember, save in local storage valid untile it expires
            localStorage.setItem('jwt', jwt);
        }
        // Set a time out for when the JWT expires.
        // The default behavior does not logout the user automatically in case the
        // app wants to do something before end. It would be easy to set a call
        // a few minutes before to alert the user to extends the session for example.
        let payload = this.getJwtPayload();
        this.jwtTimeout = setTimeout(this.jwtExpired.bind(this), (payload.exp*1000)-Date.now());
    }

    getJwt() {
        return localStorage.getItem('jwt') ?? sessionStorage.getItem('jwt') ?? null;
    }

    getJwtPayload () {
        if(!this.jwtPayload) {
            let token = this.getJwt();
            if(!token){
                return false;
            }
            let base64Url = token.split('.')[1];
            let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            let jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            this.jwtPayload = JSON.parse(jsonPayload);
        }
        return this.jwtPayload;
    };
}
