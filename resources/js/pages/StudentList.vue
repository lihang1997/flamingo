<template>
    <MainFrame>
        <el-row>
            <el-col :span='6'>
                <el-form ref='conditionForm' :model='condition' label-width='40px' inline size='small'>
                    <el-form-item label='姓名'>
                        <el-input v-model='condition.name' placeholder='输入学生的姓名.'></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type='primary' plain>搜索</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span='4' :offset='14'>
                <el-button size='small' type='danger' plain @click='isShowCreateUser = true'>新增学生信息</el-button>
            </el-col>
        </el-row>
        <el-dialog
            title='新增学生'
            :visible.sync='isShowCreateUser'
            width="40%"
        >
            <el-form :model="user" label-width='80px' size='mini'>
                <el-form-item label="姓名">
                    <el-input v-model="user.name" style='max-width: 240px;' placeholder='学生姓名.'></el-input>
                </el-form-item>
                <el-form-item label="性别">
                    <el-radio v-model="user.gender" label='F'>男</el-radio>
                    <el-radio v-model="user.gender" label='M'>女</el-radio>
                </el-form-item>
                <el-form-item label='年级'>
                    
                </el-form-item>
                <el-form-item label='备注信息'>
                    <el-input :model='user.remark' placeholder='备注信息.' style='max-width: 90%'></el-input>
                </el-form-item>
            </el-form>
        </el-dialog>
    </MainFrame>
</template>

<script>
    import MainFrame from '../components/MainFrame'
    export default {
        name: 'studentList',
        data () {
            return {
                isShowCreateUser: false,
                condition: {
                    name: ''
                },
                user: {
                    name: '',
                    gender: 'F',
                    grade: '',
                    remark: '',
                }
            }
        },
        methods: {},
        components: {
            MainFrame
        }
    }
</script>

<style lang='scss' scoped>
    .el-form .el-form-item{
        text-align: left;
    }
</style>