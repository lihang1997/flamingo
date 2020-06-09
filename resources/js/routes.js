/**
 * This file is router configurations.
 */
import Login from './pages/Login'
import Student from './pages/Student'

const routes = [
    {
        'path': '/',
        'name': 'login',
        'component': Login
    }, {
        'path': '/student',
        'name': 'student',
        'component': Student
    }
]

export default routes