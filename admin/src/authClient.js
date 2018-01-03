/* global Promise, fetch */

import { 
	AUTH_LOGIN, 
	AUTH_LOGOUT, 
	AUTH_ERROR, 
	AUTH_CHECK } from 'admin-on-rest';

export default (type, params) => {
	//return Promise.resolve();
	console.log(type);
    // called when the user attempts to log in
    if (type === AUTH_LOGIN) {
        const { username, password } = params;
		const request = new Request('/api/auth', {
            method: 'POST',
            body: JSON.stringify({
				username: username, 
				password: password 
			}),
            headers: new Headers({ 'Content-Type': 'application/json' })
        });
		
        return fetch(request)
            .then(response => {
                if (response.status < 200 || response.status >= 300) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .then(({ token }) => {
				//console.log(token);
                localStorage.setItem('token', token);
				//return Promise.resolve();
            });
    }
	
    // called when the user clicks on the logout button
    if (type === AUTH_LOGOUT) {
		const token = localStorage.getItem('token');
			
		const request = new Request('/api/auth', {
            method: 'DELETE',
            headers: new Headers({ 
				'Content-Type': 'application/json',
				token: token
			})
        });
		
        return fetch(request)
            .then(response => {
                if (response.status < 200 || response.status >= 300) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .then((token) => {
				console.log(token);
                localStorage.removeItem('token');
				//return Promise.resolve();
            });
    }
	
    // called when the API returns an error
    if (type === AUTH_ERROR) {
        const { status } = params;
		
        if (status === 401 || status === 403) {
            localStorage.removeItem('username');
            return Promise.reject();
        }
		
        return Promise.resolve();
    }
	
    // called when the user navigates to a new location
    if (type === AUTH_CHECK) {
        return localStorage.getItem('token') ? 
			Promise.resolve() : 
			Promise.reject();
    }
	
    return Promise.reject('Unknown method');
};