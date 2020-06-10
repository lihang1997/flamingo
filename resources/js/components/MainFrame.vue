<template>
    <div class="content">
        <div class='header'>
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
        </div>
        <div class='main'>
            <div class='sidebar'>
                <el-menu>
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
                        <el-menu-item index="fee-find">收费信息</el-menu-item>
                        <el-menu-item index="fee-list">收费列表</el-menu-item>
                    </el-submenu>
                </el-menu>
            </div>
            <div class='body'>
                <div class='child'>
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import service from '../service'
    export default {
        data() {
            return {
                systemName: '',
                user: {
                    name: '',
                }
            }
        },
        mounted () {
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
.content {
    height: calc(100vh);
    display: flex;
    flex-direction: column;
    .header{
        width: 100%;
        height: 60px;
        display: flex;
        flex-direction: row;
        justify-content:  space-between;
        border-bottom: solid 1px #e6e6e6;
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
            padding-right: 40px;
            .dropdown-link{
                font-size: 16px;
            }
        }
    }
    .main{
        width: 100%;
        flex: 1;
        display: flex;
        .sidebar {
            min-width: 200px;
            height: 100%;
            float: left;
            border-right: solid 1px #e6e6e6;
        }
        .body{
            align-self: flex-end;
            width: 100%;
            height: 100%;
            .child{
                width: calc(100% - 32px);
                height: calc(100% - 32px);
                margin: 16px;
            }
        }
    }
}
</style>
