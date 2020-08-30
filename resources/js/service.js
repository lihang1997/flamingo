/**
 * This file is services url configurations.
 */

const urls = {
    login: '/api/user/login',
    userInfo: '/api/user/info',
    grade: '/api/config/get?name=grade',
    searchStudent: '/api/students/search',
    createStudent: '/api/students/create',
    updateStudent: '/api/students/update',
    class: '/api/config/get?name=class',
    feeSetting: '/api/config/feeSetting',
    feeSearch: '/api/config/search',
    feeUpdate: '/api/config/feeUpdate',
    incomeAdd: '/api/income/add',
    incomeSearch: '/api/income/search',
    incomePrintOnce: '/api/income/printOnce',
    incomeUpdate: '/api/income/update',
}

export default urls