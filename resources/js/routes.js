/**
 * This file is router configurations.
 */
import Login from './pages/Login'
import StudentList from './pages/StudentList'
import FeeModify from './pages/FeeModify'
import FeeFind from './pages/FeeFind'

const routes = [
    {
        'path': '/',
        'name': 'login',
        'component': Login
    }, {
        'path': '/student-list',
        'name': 'studentList',
        'component': StudentList
    }, {
        'path': '/fee-modify',
        'name': 'feeModify',
        'component': FeeModify,
    }, {
        'path': '/fee-find',
        'name': 'feeFind',
        'component': FeeFind,
    }
]

export default routes