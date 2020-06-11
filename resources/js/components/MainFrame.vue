<template>
    <el-container>
        <el-header>
            <div class='system'>{{systemName}}</div>
            <div class='user'>
                <el-dropdown>
                    <span class="dropdown-link">
                        {{user.name}}<i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item>不知道这里干什么,先留着吧</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </el-header>
        <el-container>
            <el-aside width='200px'>
                <el-menu
                    :default-openeds='openeds'
                    :default-active='active'
                    :router='true'
                    @select='(key) => {active = key}'
                >
                    <el-submenu index="student">
                        <template slot="title">
                            <i class="el-icon-user"></i>
                            <span>学生信息管理</span>
                        </template>
                        <el-menu-item index="student-list">信息列表</el-menu-item>
                    </el-submenu>
                    <el-submenu index="fee">
                        <template slot="title">
                            <i class="el-icon-lock"></i>
                            <span>收费信息管理</span>
                        </template>
                        <el-menu-item index="fee-modify">收费增改</el-menu-item>
                        <el-menu-item index="fee-find">收费查询</el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-aside>
            <el-main>
                <slot></slot>
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
                }
            }
        },
        created () {
            this.active = this.$route.path.replace('/', '')
            axios.get(service.userInfo).then((response) => {
                let responseBody = response.data || {}
                if (responseBody && -responseBody.errorCode === 0) {
                    this.systemName = responseBody.body.systemName
                    this.user.name = responseBody.body.name
                }
            })
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
            width: 160px;
            line-height: 60px;
            align-self: flex-end;
            padding-right: 20px;
            .dropdown-link{
                font-size: 16px;
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
