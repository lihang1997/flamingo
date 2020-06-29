<template>
    <el-container>
        <el-header>
            <div class='system'>{{systemName}}</div>
            <div class='user'>
                <div class='name'>
                    <el-dropdown trigger='click'>
                        <span class="dropdown-link">
                            欢迎, {{user.name}}
                            <i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native='logout'>安全登出</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                    <div class='avatar'>
                        <el-avatar shape='square'> {{user.firstName}} </el-avatar>
                    </div>
                </div>
            </div>
        </el-header>
        <el-container>
            <el-aside width='200px'>
                <el-menu
                    :default-openeds='openeds'
                    :default-active='active'
                    router
                    @select='(key) => {active = key}'
                >
                    <el-submenu index="student">
                        <template slot="title">
                            <i class="el-icon-user"></i>
                            <span>学生信息管理</span>
                        </template>
                        <el-menu-item index='/student/list' key='/student/list'>信息列表</el-menu-item>
                    </el-submenu>
                    <el-submenu index="fee">
                        <template slot="title">
                            <i class="el-icon-lock"></i>
                            <span>收费信息管理</span>
                        </template>
                        <el-menu-item index='/fee/find' key='/fee/find'>收费查询</el-menu-item>
                        <el-menu-item index='/fee/detail' key='/fee/detail'>费用详情</el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-aside>
            <el-main>
                <router-view></router-view>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    import service from '../service'
    export default {
        data() {
            return {
                openeds: ['student', 'fee'],
                active: '',
                systemName: '',
                user: {
                    name: '',
                    firstName: '',
                }
            }
        },
        created () {
            this.active = this.$route.path
            axios.get(service.userInfo).then((response) => {
                let responseBody = response.data || {}
                if (responseBody && -responseBody.errorCode === 0) {
                    this.systemName = responseBody.body.systemName
                    this.user.name = responseBody.body.name
                    this.user.firstName = this.user.name.charAt(0).toUpperCase()
                }
            })
        },
        methods: {
            logout () {
                sessionStorage.removeItem('loginToken')
                this.$router.push({name: 'login'})
            }
        }
    }
</script>

<style lang='scss' scope>
    .el-header{
        display: flex;
        flex-direction: row;
        justify-content:  space-between;
        border-bottom: solid 1px #e6e6e6;
        line-height: 60px;
        .system{
            min-width: 200px;
            line-height: 60px;
            margin-left: 40px;
            font-size: 32px;
            font-weight: bold;
        }
        .user{
            height: 100%;
            align-self: flex-end;
            line-height: 60px;
            .name{
                min-width: 160px;
                .dropdown-link{
                    font-size: 16px;
                }
                .avatar{
                    margin-left: 8px;
                    width: 60px;
                    height: 60px;
                    float: right;
                    .el-avatar{
                        transform: translate(0, 10px);
                    }
                }
            }
        }
    }
    .el-aside{
        height: calc(100vh - 60px);
        border-right: solid 1px #e6e6e6;
    }
    .el-main{
        height: calc(100vh - 60px);
    }
</style>
