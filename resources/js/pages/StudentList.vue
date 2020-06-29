<template>
    <div>
        <el-row>
            <el-col :span='6'>
                <el-form ref='conditionForm' :model='condition' label-width='40px' inline size='small'>
                    <el-form-item label='姓名'>
                        <el-input 
                            v-model='condition.name' 
                            placeholder='输入学生的姓名.'
                            @keyup.enter.native='search'
                        >
                        </el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type='primary' plain @click='search'>搜索</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span='2' :offset='16'>
                <el-button size='small' type='danger' plain @click='isShowCreateUser = true'>新增学生信息</el-button>
            </el-col>
        </el-row>
        <el-row>
            <el-table
                :data='filterStudent()'
                border
                stripe
                size='mini'
                highlight-current-row
                :cell-style='witchStyle'
            >
                <el-table-column
                    prop='id'
                    label='ID'
                    width='80'
                >
                </el-table-column>
                <el-table-column
                    prop='name'
                    label='姓名'
                    width='120'
                >
                </el-table-column>
                <el-table-column
                    prop='gender'
                    label='性别'
                    width='80'
                    :formatter='genderFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='grade'
                    label='年级'
                    width='120'
                    :formatter='gradeFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='phone'
                    label='家长电话'
                    width='120'
                >
                </el-table-column>
                <el-table-column
                    prop='remark'
                    label='备注信息'
                    show-overflow-tooltip
                >
                </el-table-column>
                <el-table-column
                    prop='create'
                    label='创建者信息'
                    width='120'
                    show-overflow-tooltip
                    :formatter='operatorFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='update'
                    label='更新者信息'
                    width='120'
                    show-overflow-tooltip
                    :formatter='operatorFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='status'
                    label='状态'
                    width='80'
                    :formatter='statusFormatter'
                >
                </el-table-column>
                <el-table-column align='center' width=240>
                    <template slot='header' slot-scope='scope'>
                        <el-input
                            v-model='filter'
                            size='mini'
                            placeholder='输入关键字搜索'
                            @input='filterStudent(scope)'
                        >
                        </el-input>
                    </template>
                    <template slot-scope='scope'>
                        <el-button
                            size='mini'
                            plain
                            type='primary'
                            @click='incomeFee(scope.row)'>
                            收费
                        </el-button>
                        <el-button
                            size='mini'
                            plain
                            type='info'
                            @click='showUpdateUserDialog(scope.row)'
                        >
                            编辑
                        </el-button>
                        <el-button
                            size='mini'
                            plain
                            :type="scope.row.status == 0 ? 'danger' : 'warning'"
                            @click='disable(scope.row)'
                        >
                            {{scope.row.status == 0 ? '禁用' : '恢复'}}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-dialog
            title='新增学生'
            :visible.sync='isShowCreateUser'
            width="60%">
            <el-form :model='user' ref='createUser' label-width='80px' size='mini'>
                <el-form-item 
                    label='姓名'
                    prop='name'
                    :rules="[
                        {required: true, message: '必须填写姓名.', trigger: 'blur'}
                    ]"
                >
                    <el-input v-model='user.name' style='max-width: 240px;' placeholder='学生姓名.'>
                    </el-input>
                </el-form-item>
                <el-form-item label='性别'>
                    <el-radio v-model='user.gender' label='F'>男</el-radio>
                    <el-radio v-model='user.gender' label='M'>女</el-radio>
                </el-form-item>
                <el-form-item label='年级'>
                    <el-radio-group v-model='user.grade'>
                        <el-radio
                            v-for='item in gradeList' 
                            :index='item.key' 
                            :key='item.key'
                            :label='item.key'
                        >
                            {{item.value}}
                        </el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label='家长电话'>
                    <el-input v-model='user.phone' style='max-width: 360px' placeholder='家长电话.'>
                    </el-input>
                </el-form-item>
                <el-form-item label='备注信息'>
                    <el-input type='textarea' v-model='user.remark' placeholder='备注信息.' style='max-width: 95%'></el-input>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' plain @click='isShowCreateUser = false'>取 消</el-button>
                <el-button size='small' type='success' plain @click=createStudent>提 交</el-button>
            </div>
        </el-dialog>

        <el-dialog
            title='修改学生信息'
            :visible.sync='isShowUserEdit'
            width="60%">
            <el-form :model='user' ref='updateUser' label-width='80px' size='mini'>
                <el-form-item label='姓名'>
                    <el-input v-model='updateUser.name' style='max-width: 240px;' placeholder='学生姓名.' disabled>
                    </el-input>
                </el-form-item>
                <el-form-item label='性别'>
                    <el-radio v-model='updateUser.gender' label='F' disabled>男</el-radio>
                    <el-radio v-model='updateUser.gender' label='M' disabled>女</el-radio>
                </el-form-item>
                <el-form-item label='年级'>
                    <el-radio-group v-model='updateUser.grade'>
                        <el-radio
                            v-for='item in gradeList' 
                            :index='item.key' 
                            :key='item.key'
                            :label='item.key'
                        >
                            {{item.value}}
                        </el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label='家长电话'>
                    <el-input v-model='updateUser.phone' style='max-width: 360px' placeholder='家长电话.'>
                    </el-input>
                </el-form-item>
                <el-form-item label='备注信息'>
                    <el-input type='textarea' v-model='updateUser.remark' placeholder='备注信息.' style='max-width: 95%'></el-input>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' plain @click='isShowUserEdit = false'>取 消</el-button>
                <el-button size='small' type='success' plain @click=updateStudent>提 交</el-button>
            </div>
        </el-dialog>

        <el-dialog
            :title='feeTitle'
            :visible.sync='isShowIncomeFee'
            width="60%">
            <el-form :model='income' ref='incomeFee' label-width='80px' size='mini'>
                <el-form-item label='备注信息'>
                    <el-input type='textarea' v-model='updateUser.remark' placeholder='备注信息.' style='max-width: 95%'></el-input>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' plain @click='isShowIncomeFee = false'>取 消</el-button>
                <el-button size='small' type='success' plain @click=incomeFee>提 交</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import service from '../service'
    export default {
        name: 'studentList',
        data () {
            return {
                isShowCreateUser: false,
                isShowUserEdit: false,
                isShowIncomeFee: false,
                feeTitle: '',
                condition: {
                    name: ''
                },
                filter: '',
                user: {
                    name: '',
                    gender: 'F',
                    grade: '',
                    phone: '',
                    remark: '',
                },
                updateUser: {
                    id: '',
                    name: '',
                    gender: '',
                    grade: '',
                    phone: '',
                    remark: '',
                    status: '',
                },
                income: {
                    name: '',
                    month: [],
                    total: '',
                },
                gradeList: [],
                studentList: []
            }
        },
        created () {
            axios.get(service.grade).then((response) => {
                const responseBody = response.data
                if (-responseBody.errorCode === 0) {
                    this.gradeList = responseBody.body
                }
            }).then(()=> {
                this.search()
            })
        },
        methods: {
            search () {
                axios.get(service.searchStudent + '?' + 'name=' + this.condition.name).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.studentList = responseBody.body
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            createStudent () {
                this.$refs.createUser.validate((valid) => {
                    if (valid) {
                        axios.post(service.createStudent, this.user).then((response) => {
                            const responseBody = response.data
                            if (-responseBody.errorCode === 0) {
                                this.$message.success('添加成功.')
                            } else {
                                this.$message.error(responseBody.errorMessage)
                                this.condition.name = this.user.name
                            }
                            this.search()
                            this.isShowCreateUser = false
                        })
                    } else {
                        this.$message.error('请填写完整，姓名必须填写.')
                    }
                })
            },
            updateStudent () {
                axios.post(service.updateStudent, this.updateUser).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('更新成功.')
                        this.search()
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                    this.isShowUserEdit = false
                })
            },
            genderFormatter (row) {
                const gender = row.gender
                return gender === 'F' ? '男' : '女'
            },
            gradeFormatter (row) {
                const grade = row.grade
                const gradeInfo = _.filter(this.gradeList, (item) => {
                    return item.key === grade
                })
                const name = gradeInfo[0].value
                return name
            },
            statusFormatter (row) {
                const status = row.status
                return status === 0 ? '正常' : '禁用'
            },
            operatorFormatter(row, column){
                let name = row[column.property + 'User']
                let time = row[column.property + 'Time']
                time = this.$moment(time * 1000).format('YYYY-MM-DD HH:mm:ss')
                return name + '<' + time + '>'
            },
            witchStyle (context) {
                let style = ''
                if (context.column.property === 'status') {
                    let color = ''
                    if (context.row.status === 0) {
                        color = 'green'
                    } else {
                        color = 'red'
                    }
                    style = 'color: ' + color
                }
                return style
            },
            filterStudent () {
                return this.studentList.filter((row) => {
                    const gradeInfo = _.filter(this.gradeList, (item) => {
                        return item.key === row.grade
                    })
                    const grade = gradeInfo[0].value
                    const gender = row.gender === 'F' ? '男' : '女'
                    const status = row.status === 0 ? '正常' : '禁用'
                    return row.name.indexOf(this.filter) !== -1
                        || grade.indexOf(this.filter) !== -1
                        || gender.indexOf(this.filter) !== -1
                        || row.phone.indexOf(this.filter) !== -1
                        || row.remark.indexOf(this.filter) !== -1
                        || row.createUser.indexOf(this.filter) !== -1
                        || row.updateUser.indexOf(this.filter) !== -1
                        || status.indexOf(this.filter) !== -1
                })
            },
            showUpdateUserDialog (row) {
                this.updateUser.id = row.id
                this.updateUser.grade = row.grade
                this.updateUser.name = row.name
                this.updateUser.gender = row.gender
                this.updateUser.phone = row.phone
                this.updateUser.remark = row.remark
                this.updateUser.status = row.status
                this.isShowUserEdit = true
            },
            disable (row) {
                const post = {id: +row.id, status: +!row.status}
                axios.post(service.updateStudent, post).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('更新成功.')
                        this.search()
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            incomeFee (row) {
                this.isShowIncomeFee = true
                const gradeInfo = _.filter(this.gradeList, (item) => {
                    return item.key === row.grade
                })
                const gradeName = gradeInfo[0].value
                this.feeTitle = row.name + ' ' + gradeName + ' 缴费信息'
            }
        },
    }
</script>

<style lang='scss' scoped>
    .el-form .el-form-item{
        text-align: left;
    }
</style>
