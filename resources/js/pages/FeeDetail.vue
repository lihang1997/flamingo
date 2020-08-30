<template>
    <div>
        <el-row>
            <el-col :span='16'>
                <el-form ref='conditionForm' :model='condition' label-width='60px' inline size='small'>
                    <el-form-item label='时间'>
                        <el-date-picker
                            v-model='condition.when'
                            type='monthrange'
                            align='left'
                            unlink-panels
                            range-separator='至'
                            start-placeholder='开始月份'
                            end-placeholder='结束月份'
                            editable
                            :picker-options="pickerOptions">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model='condition.grade' clearable placeholder='选择年级(可不选)' style='width: 180px'>
                            <el-option 
                            v-for='item in grade'
                            :key='item.key'
                            :value='item.key'
                            :label='item.value'>
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model='condition.status' clearable placeholder='数据状态.' style='width: 120px'>
                            <el-option key='0' value='0' label='有效'></el-option>
                            <el-option key='1' value='1' label='已删除'></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type='primary' plain @click='search'>搜索</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span='2' :offset='6'>
                <el-button size='small' type='danger' plain @click='isShowFeeAdd = true'>新增收费明细</el-button>
            </el-col>
        </el-row>

        <el-row>
            <el-table
                :data='feeFilter()'
                border
                stripe
                size='mini'
                highlight-current-row
                :height='height'
                :cell-style='witchStyle'
            >
                <el-table-column
                    prop='id'
                    label='ID'
                    width='80'
                >
                </el-table-column>
                <el-table-column
                    prop='key'
                    label='时间'
                    width='120'
                    sortable
                    :sort-method='sortMonth'
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
                    prop='category'
                    label='费用名称'
                    width='120'
                >
                    <template slot-scope="scope">
                        <i :class="getCategoetIconClassName(scope.row)"></i>
                        {{categoryFormatter(scope.row)}}
                    </template>
                </el-table-column>
                <el-table-column
                    prop='value'
                    label='费用(元)'
                    width='140'
                    sortable
                    :sort-method='sortFee'
                >
                    <template slot-scope="scope">
                        <span v-if=' ! isEdit(scope.row)'>{{scope.row.value}}</span>
                        <el-input
                            v-else
                            ref='editForm'
                            size='mini'
                            autofocus
                            v-model='currentEditRow.value'
                        >
                        </el-input>
                    </template>
                </el-table-column>
                <el-table-column
                    prop='create'
                    label='创建者信息'
                    show-overflow-tooltip
                    :formatter='operatorFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='update'
                    label='更新者信息'
                    show-overflow-tooltip
                    :formatter='operatorFormatter'
                >
                </el-table-column>
                <el-table-column
                    prop='status'
                    label='状态'
                    show-overflow-tooltip
                    :formatter='statusFormater'
                >
                </el-table-column>
                <el-table-column align='center' width=240>
                    <template slot='header' slot-scope='scope'>
                        <el-input
                            v-model='filter'
                            size='mini'
                            placeholder='输入关键字搜索'
                            @input='feeFilter(scope)'
                        >
                        </el-input>
                    </template>
                    <template slot-scope='scope'>
                        <template v-if='isEdit(scope.row)'>
                            <el-button
                                size='mini'
                                plain
                                type='primary'
                                @click='confirmEdit()'>
                                确认
                            </el-button>
                            <el-button
                                size='mini'
                                plain
                                type='info'
                                @click='currentEditRow = {}'>
                                放弃
                            </el-button>
                        </template>
                        <el-button
                            v-else
                            size='mini'
                            plain
                            type='primary'
                            @click='edit(scope.row)'>
                            编辑
                        </el-button>
                        <el-button
                            v-if='-scope.row.status === 0'
                            size='mini'
                            plain
                            type='danger'
                            @click='updateStatus(scope.row, 1)'
                        >
                            删除
                        </el-button>
                        <el-button
                            v-else
                            size='mini'
                            plain
                            type='warning'
                            @click='updateStatus(scope.row, 0)'
                        >
                            恢复
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-row>
            <el-dialog
            title='添加费用明细'
            :visible.sync='isShowFeeAdd'
            width='60%'>
            <el-form :model='feeAdd' label-width='120px' size='mini'>
                <el-form-item label='费用生效时间'>
                    <el-date-picker
                        v-model='feeAdd.when'
                        type='monthrange'
                        align='center'
                        unlink-panels
                        range-separator='至'
                        start-placeholder='开始月份'
                        end-placeholder='结束月份'
                        editable
                        clearable>
                    </el-date-picker>
                </el-form-item>
                <el-form-item label='年级'>
                    <el-checkbox-group v-model='feeAdd.grade'>
                        <el-checkbox
                        v-for='item in grade'
                        :key='item.key'
                        :value='item.key'
                        :label='item.key'>
                        {{item.value}}
                        </el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item
                v-for='(item, idx) in feeAdd.grade'
                :key='idx'
                :index='idx'
                :label='grade[item].value'>
                    <el-row v-if='hasInit'>
                        <el-col :span='6' style='margin-right: 8px;'
                            v-for='(classOne, idxClass) in clazz' 
                            :index='idxClass'
                            :key='idxClass'
                            :value='idxClass'>
                                <el-input
                                v-model="feeAdd.fees[item + '_' + classOne.key]"
                                autofocus
                                type='number'
                                min=1
                                :placeholder="classOne.value + '费用.'">
                                    <template slot="append">元</template>
                                </el-input>
                        </el-col>
                    </el-row>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' type='info' plain @click='isShowFeeAdd = false'>取 消</el-button>
                <el-popover
                    width="500"
                    placement="top-end"
                    v-model='isShowFeeTips'
                    @show='makeDetailTips'
                    :open-delay='1000'
                >
                    <p style='color:red'>{{feeTips}}</p>
                    <el-table
                        highlight-current-row
                        stripe
                        :data='feeDetailTips'
                        size='mini'>
                            <el-table-column
                            prop='grade'
                            label='年级'>
                            </el-table-column>
                            <el-table-column
                            v-for='item in clazz'
                            :key='item.key'
                            :value='item.key'
                            :label='item.value'
                            :prop='item.key'>
                            </el-table-column>
                    </el-table>
                    <div style="text-align: right; margin: 16px 0 0 0">
                        <el-button size="mini" type="info" plain @click="isShowFeeTips = false">取 消</el-button>
                        <el-button type="primary" size="mini" plain @click="saveFeeDetail">保 存</el-button>
                    </div>
                    <el-button size='small' type='success' plain slot='reference'>提 交</el-button>
                </el-popover>
            </div>
        </el-dialog>
        </el-row>
    </div>
</template>

<script>
    import service from '../service'
    export default {
        name: 'feeDetail',
        data () {
            return {
                grade: {},
                clazz: {},
                height: window.screen.height - 260,
                hasInit: false,
                pickerOptions: {
                    shortcuts: [{
                        text: '今年至今',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date(new Date().getFullYear(), 0)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近六个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setMonth(start.getMonth() - 5)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近十二个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setMonth(start.getMonth() - 11)
                            picker.$emit('pick', [start, end])
                        }
                    }]
                },
                condition: {
                    when: [],
                    grade: '',
                    status: '',
                },
                feeAdd: {
                    when: [],
                    grade: [],
                    fees: {},
                },
                isShowFeeAdd: false,
                isShowFeeTips: false,
                feeTips: '',
                feeDetailTips: [],
                feeList: [],
                filter: '',
                currentEditRow: {},
            }
        },
        created () {
            const end = new Date();
            const start = new Date();
            start.setMonth(start.getMonth() - 5)
            this.condition.when = [start, end]
            const startForFee = new Date()
            const endForFee = new Date()
            endForFee.setMonth(startForFee.getMonth() + 3)
            this.feeAdd.when = [startForFee, endForFee]
            this.condition.status = '0'
            Promise.all([
                axios.get(service.grade),
                axios.get(service.class)
            ]).then((response) => {
                const gradeResponse = response[0].data
                if (-gradeResponse.errorCode === 0) {
                    this.grade = _.keyBy(gradeResponse.body, 'key')
                } else {
                    this.$message.error(gradeResponse.errorMessage)
                }
                const classResponse = response[1].data
                if (-classResponse.errorCode === 0) {
                    this.clazz = _.keyBy(classResponse.body, 'key')
                } else {
                    this.$message.error(classResponse.errorMessage)
                }
                this.hasInit = true
            }).then(() => {
                this.search()
            })
        },
        methods: {
            search () {
                const parameters = {
                    grade: this.condition.grade,
                }
                if (this.condition.status !== '') {
                    parameters.status = this.condition.status
                }
                let start = new Date(this.condition.when[0]).getTime()
                let end = new Date(this.condition.when[1]).getTime()
                parameters.start = start / 1000 | 0
                parameters.end = end / 1000 | 0
                axios.post(service.feeSearch, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.feeList = responseBody.body
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            feeFilter() {
                return this.feeList.filter((row) => {
                    const grade = this.grade[row.grade].value
                    const title = this.clazz[row.category].value
                    return row.key.indexOf(this.filter) !== -1
                        || grade.indexOf(this.filter) !== -1
                        || title.indexOf(this.filter) !== -1
                        || row.value.indexOf(this.filter) !== -1
                        || row.createUser.indexOf(this.filter) !== -1
                        || row.updateUser.indexOf(this.filter) !== -1
                })
            },
            makeDetailTips () {
                let startTimestamp = this.feeAdd.when[0]
                let endTimestamp = this.feeAdd.when[1]
                const start = this.$moment(startTimestamp).format('YYYY-MM')
                const end = this.$moment(endTimestamp).format('YYYY-MM')
                let tips = '本次操作将保存' + start + '至' + end + '的费用信息,请注意核对是否有误~'
                this.feeTips = tips
                this.feeDetailTips = []
                this.feeAdd.grade.forEach((gradeKey) => {
                    const row = {
                        grade: this.grade[gradeKey].value,
                    }
                    let testing = 0
                    Object.values(this.clazz).forEach((clazz) => {
                        row[clazz.key] = this.feeAdd.fees[[gradeKey + '_' + clazz.key]] || ''
                        testing += row[clazz.key]
                    })
                    if (testing > 0) {
                        this.feeDetailTips.push(row)
                    }
                })
            },
            saveFeeDetail () {
                const validtor = _.filter(this.feeAdd.fees, (item) => {
                    return !!item
                })
                if (this.feeAdd.grade.length > 0 && validtor.length > 0) {
                    const parameters = _.cloneDeep(this.feeAdd)
                    let start = new Date(this.feeAdd.when[0]).getTime()
                    let end = new Date(this.feeAdd.when[1]).getTime()
                    parameters.when[0] = start / 1000 | 0
                    parameters.when[1] = end / 1000 | 0
                    axios.post(service.feeSetting, parameters).then((response) => {
                        const responseBody = response.data
                        if (-responseBody.errorCode === 0) {
                            this.$message.success(responseBody.body.message)
                        } else {
                            this.$message.error(responseBody.errorMessage)
                        }
                    })
                } else {
                    this.$message.error('没有设置对应信息，就不要保存~')
                }
            },
            operatorFormatter(row, column){
                let name = row[column.property + 'User']
                let time = row[column.property + 'Time']
                time = this.$moment(time * 1000).format('YYYY-MM-DD HH:mm:ss')
                return name + '<' + time + '>'
            },
            gradeFormatter (row) {
                const name = this.grade[row.grade].value
                return name
            },
            categoryFormatter (row) {
                const category = row.category
                return this.clazz[category].value
            },
            statusFormater (row) {
                return -row.status === 0 ? '正常' : '已删除'
            },
            edit (row) {
                this.currentEditRow = _.cloneDeep(row)
                this.$nextTick(() => {
                    this.$refs.editForm.focus()
                })
            },
            updateStatus (row, status) {
                const parametes = {
                    'id': row.id,
                    'update': {
                        'status': status,
                    }
                }
                axios.post(service.feeUpdate, parametes).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('变更成功~')
                        // 更新本地数据
                        let length = this.feeList.length
                        for (let index = 0; index < length; index++) {
                            if (this.feeList[index].id === parametes.id) {
                                this.feeList[index].status = status
                                break
                            }
                        }
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            sortFee (first, second) {
                return +first.value < +second.value ? -1 : 1
            },
            sortMonth (first, second) {
                return +first.key < +second.key ? -1 : 1
            },
            getCategoetIconClassName (row) {
                const classMapper = {
                    afternoon: 'el-icon-sunny',
                    evening: 'el-icon-sunset',
                    all: 'el-icon-school'
                }
                const defaultClassName = 'el-icon-more'
                const category = row.category
                if (classMapper[category]) {
                    return classMapper[category]
                } else {
                    return defaultClassName
                }
            },
            isEdit (row) {
                return row.id === this.currentEditRow.id
            },
            confirmEdit () {
                const parameters = {
                    'id': this.currentEditRow.id,
                    'update': {
                        'value': this.currentEditRow.value
                    }
                }
                axios.post(service.feeUpdate, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('更新成功~')
                        // 更新本地数据
                        let length = this.feeList.length
                        for (const index = 0; index < length; index++) {
                            if (this.feeList[index].id === this.currentEditRow.id) {
                                this.feeList[index].value = this.currentEditRow.value
                                break
                            }
                        }
                        this.currentEditRow = {}
                        this.isShowFeeAdd = false
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
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
        },
    }
</script>