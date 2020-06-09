<template>
    <div class='login-container'>
        <el-form 
            :model='user' :rules='rules'
            status-icon
            ref='login'
            label-position='left' 
            label-width='0px'
            class='login login-page'>
                <h3 class='title'>Flamingo</h3>
                <el-form-item prop='username'>
                    <el-input type='text'
                        v-model='user.username' 
                        placeholder='用户名'
                    ></el-input>
                </el-form-item>
                <el-form-item prop='password'>
                    <el-input type='password' 
                        v-model='user.password' 
                        placeholder='密码'
                    ></el-input>
                </el-form-item>
                <el-form-item style='width:100%;'>
                    <el-button type='primary' style='width:100%;' @click='loginAction' :loading='logining'>登&nbsp;&nbsp;录</el-button>
                </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import service from '../service'
    export default {
        name: 'Login',
        data: () => {
            return {
                logining: false,
                user: {
                    username: '',
                    password: '',
                },
                rules: {
                    username: [
                        {
                            required: true,
                            message: '请输入账号.',
                            trigger: 'blur'
                        }
                    ],
                    password: [
                        {
                            required: true,
                            message: '请输入密码.',
                            trigger: 'blur'
                        }
                    ]
                }
            }
        },
        methods: {
            loginAction () {
                this.$refs.login.validate((valid) => {
                    this.logining = true
                    if (valid) {
                        axios.post(service.login, this.user).then((response) => {
                            response = response.data
                            if (-response.errorCode === 0) {
                                this.$router.push({path: '/student'})
                            } else {
                                this.$message({type: 'error', message: response.errorMessage})
                            }
                            this.logining = false
                        }, (error) => {
                            this.$message({type: 'error', message: '需要重新登录.'})
                            this.logining = false
                        })
                    } else {
                        this.logining = false
                        return false
                    }
                })
            }
        }
    }
</script>
 
<style scoped>
    .login-container {
        width: 100%;
        height: 100%;
    }
    .login-page {
        -webkit-border-radius: 8px;
        border-radius: 8px;
        margin: 180px auto;
        width: 360px;
        padding: 16px 36px 16px;
        background: #fff;
        border: 1px solid #eaeaea;
        box-shadow: 0 0 24px #cac6c6;
    }
    label.el-checkbox.rememberme {
        margin: 0px 0px 16px;
        text-align: left;
    }
</style>