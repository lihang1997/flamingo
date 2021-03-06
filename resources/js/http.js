/**
 * This file is setting for http request interceptors.
 */

window.axios.interceptors.request.use(
    config => {
        const token = sessionStorage.getItem('loginToken');
        if (token) {
            config.headers.LoginToken = token
        }
        return config
    },
    err => {
        return Promise.reject(err)
    }
)

window.axios.interceptors.response.use(
    response => {
        const headers = response.headers || {}
        const responseBody = response.data || {}
        if (responseBody && -responseBody.errorCode === 0 && headers['login-token']) {
            sessionStorage.setItem('loginToken', headers['login-token'])
        }
        return response
    },
    error => {
        const httpCode = error.response.status
        if (httpCode === 403) {
            window.location.href = axios.apiHost
        } else {
            console.log('发生了未知的错误.')
        }
        return Promise.reject(error) 
    }
)
