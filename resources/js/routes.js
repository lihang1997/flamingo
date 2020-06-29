/**
 * This file is router configurations.
 */
import Login from './pages/Login'
import MainFrame from './components/MainFrame'
import StudentList from './pages/StudentList'
import FeeDetail from './pages/FeeDetail'
import FeeFind from './pages/FeeFind'

const routes = [
    {
        'path': '/',
        'name': 'login',
        'component': Login
    }, {
        'path': '/student',
        'name': 'student',
        'hidden': true,
        'component': MainFrame,
        'children': [
            {
                'path': 'list',
                'name': 'list',
                'component': StudentList,
            }
        ]
    }, {
        'path': '/fee',
        'name' : 'fee',
        'hidden': true,
        'component': MainFrame,
        'children': [
            {
                'path': 'detail',
                'name': 'detail',
                'component': FeeDetail
            }, {
                'path': 'find',
                'name': 'find',
                'component': FeeFind
            }
        ]
    }
]

export default routes