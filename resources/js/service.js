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
}

export default urls