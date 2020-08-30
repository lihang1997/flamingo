<template>
    <div>
        <el-collapse v-model="showSearchBox">
            <el-collapse-item title="设置筛选条件" name="condition">
                <div style="width: 500px">
                    <el-form
                        label-position="right"
                        label-width="120px" 
                        :model="condition">
                            <el-form-item label="收费ID">
                                <el-input size='mini' v-model='condition.orderId' placeholder="在这里填入收费ID(可不填)."></el-input>
                            </el-form-item>
                            <el-form-item label="学生姓名">
                                <el-input size='mini' v-model='condition.studentName' placeholder="在这里填入学生姓名(可不填)."></el-input>
                            </el-form-item>
                            <el-form-item label="学生年级">
                                <el-select v-model='condition.grade' size='mini' clearable placeholder='选择年级(可不选)' style='width: 180px'>
                                    <el-option 
                                    v-for='item in gradeInfo'
                                    :key='item.key'
                                    :value='item.key'
                                    :label='item.value'>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label='创建时间'>
                                <el-date-picker
                                    v-model='condition.when'
                                    type="daterange"
                                    align='left'
                                    unlink-panels
                                    range-separator='至'
                                    start-placeholder='开始日期'
                                    end-placeholder='结束日期'
                                    editable
                                    size='mini'
                                    :picker-options="pickerOptions">
                                </el-date-picker>
                            </el-form-item>
                            <el-form-item label="状态">
                                <el-radio-group v-model="condition.status" size="mini">
                                    <el-radio label="0">收费成功</el-radio>
                                    <el-radio label="1">已经退款</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="是否优惠">
                                <el-radio-group v-model="condition.discount" size="mini">
                                    <el-radio label="1">是(实收&lt;应收)</el-radio>
                                    <el-radio label="2">否(实收=应收)</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="是否有备注">
                                <el-radio-group v-model="condition.hasRemark" size="mini">
                                    <el-radio label="1">是</el-radio>
                                    <el-radio label="2">否</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="是否已删除">
                                <el-radio-group v-model="condition.isDelete" size="mini">
                                    <el-radio label="1">是</el-radio>
                                    <el-radio label="0">否</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <div style="padding-left: 120px">
                                <el-button type="primary" size='mini' @click="search()">查询</el-button>
                                <el-button size='mini' @click="resetCondition">重置</el-button>
                            </div>
                    </el-form>
                </div>
            </el-collapse-item>
        </el-collapse>
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
                prop='orderId'
                label='收费ID'
                width='120'
            >
            </el-table-column>
            <el-table-column
                prop="studentName"
                label='姓名'
                width="100"
            >
            </el-table-column>
            <el-table-column
                prop='grade'
                label='年级'
                width='80'
                :formatter='gradeFormatter'
            >
            </el-table-column>
            <el-table-column
                prop='total'
                label='应收金额'
                width='100'
                :formatter='amountFormatter'
            >
            </el-table-column>
            <el-table-column
                prop='real'
                label='实际金额'
                width='100'
                :formatter='amountFormatter'
            >
            </el-table-column>
            <el-table-column
                label='费用详情'
                width="80"
            >
                <slot slot-scope='detail'>
                    <el-popover
                        placement="right"
                        width="300"
                        trigger="hover">
                        <el-table
                            :data='JSON.parse(detail.row.feeDetail)'
                        >
                            <el-table-column prop="month" width="100" label="日期"></el-table-column>
                            <el-table-column width="100" label="类型" :formatter="feeTypeFormatter"></el-table-column>
                            <el-table-column width="100" label="金额" :formatter="detailAmountFormatter"></el-table-column>
                        </el-table>
                        <el-button slot="reference" size='mini' type='text'>费用详情</el-button>
                    </el-popover>
                </slot>
            </el-table-column>
            <el-table-column
                prop='remark'
                label='备注信息'
                show-overflow-tooltip
            >
            </el-table-column>
            <el-table-column
                prop='invoice'
                width="80"
                label='收据'
                :formatter='invoiceFormatter'
            >
            </el-table-column>
            <el-table-column
                prop='status'
                label='状态'
                width='80'
                :formatter='statusFormatter'
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
                    <el-button
                        size='mini'
                        plain
                        type='info'
                        @click='showUpdateDialog(scope.row)'
                    >
                        编辑
                    </el-button>
                    <el-button
                        size='mini'
                        plain
                        type='primary'
                        @click='printOnce(scope.row)'>
                        重新打印
                    </el-button>
                    <el-button
                        size='mini'
                        plain
                        :type="scope.row.isDelete == 0 ? 'danger' : 'warning'"
                        @click='disable(scope.row)'
                    >
                        {{scope.row.isDelete == 0 ? '删除' : '恢复'}}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog
            :close-on-click-modal='false'
            :title='updateDialogTitle'
            :visible.sync='isShowUpdateDialog'
            width="60%">
            <el-form :model='updateFee' ref='updateFee' label-width='80px' size='mini'>
                <el-form-item label='收费明细'>
                    <el-select 
                        v-model="updateFee.fees"
                        multiple
                        clearable
                        filterable
                        placeholder='请选择收费的明细.'
                        :loading='isLoadingFeeDetail'
                        style='min-width: 90%'
                        >
                        <el-option
                            v-for='item in feeDetailList'
                            :key='item.key'
                            :value='item.key'
                            :label='item.label'
                        >
                            <span style="float: left">{{ item.label }}</span>
                            <span style="padding-right: 16px; float: right; color: #8492a6; font-size: 13px;">{{ item.fee + '元'}}</span>
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label='费用总计'>
                    <el-row>
                        <el-col :span='8' style='padding-right: 8px'>
                            <el-input v-model='updateFee.real' placeholder='实际收费金额.'>
                                <template slot='prepend'>实</template>
                                <template slot='append'>元</template>
                            </el-input>
                        </el-col>
                        <el-col :span='8'>
                            <el-input v-model='updateFee.total' disabled placeholder='应该收费金额.'>
                                <template slot='prepend'>应</template>
                                <template slot='append'>元</template>
                            </el-input>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item label='备注信息'>
                    <el-input type='textarea' v-model='updateFee.remark' placeholder='如果实际收款金额与应收金额不同，需要填写备注原因.' style='max-width: 95%'></el-input>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' plain @click='isShowUpdateDialog = false'>取 消</el-button>
                <el-popconfirm
                    confirmButtonText='确定已经收款'
                    cancelButtonText='还没有收款'
                    confirmButtonType='danger'
                    cancelButtonType='info'
                    @onConfirm='confirmUpdateIncome'
                    icon="el-icon-info"
                    iconColor="red"
                    :title="feeTips">
                    <el-button slot='reference' size='small' type='success' plain>提 交</el-button>
                </el-popconfirm>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import service from '../service'
    export default {
        name: 'FeeFind',
        data () {
            return {
                showSearchBox: 'condition',
                gradeInfo: {},
                condition: {
                    orderId: '',
                    studentName: '',
                    when: [],
                    grade: '',
                    status: '0',
                    discount: '',
                    hasRemark: '',
                    isDelete: '0',
                },
                pickerOptions: {
                    shortcuts: [{
                        text: '今天',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date()
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近三天',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date()
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 3)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date()
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '今年至今',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date(new Date().getFullYear(), 0)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近六个月',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date()
                            start.setMonth(start.getMonth() - 5)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近十二个月',
                        onClick(picker) {
                            const end = new Date()
                            const start = new Date()
                            start.setMonth(start.getMonth() - 11)
                            picker.$emit('pick', [start, end])
                        }
                    }]
                },
                orders: [],
                clazz: {},
                height: 0,
                filter: '',
                isShowUpdateDialog: false,
                updateDialogTitle: '',
                currentItem: {},
                feeDetailList: [],
                isLoadingFeeDetail: false,
                updateFee: {
                    fees: [],
                    total: '',
                    real: '',
                    remark: '',
                },
                feeTips: '别瞎点，认真点记录收款金额~',
            }
        },
        created() {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 3)
            this.condition.when = [start, end]
            axios.get(service.grade).then((response) => {
                const responseBody = response.data
                if (-responseBody.errorCode === 0) {
                    this.gradeInfo = _.keyBy(responseBody.body, 'key')
                }
            })
        },
        methods: {
            changeCollapse () {
                this.showSearchBox = ''
            },
            search () {
                const parameters = _.cloneDeep(this.condition)
                const start = new Date(parameters.when[0])
                const end = new Date(parameters.when[1])
                parameters.when[0] = start.getTime() / 1000 | 0
                parameters.when[1] = end.getTime() / 1000 | 0
                this.height = 0
                this.orders = []
                axios.post(service.incomeSearch, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.height = window.screen.height - 260,
                        this.orders = responseBody.body.orders
                        this.clazz = _.keyBy(responseBody.body.clazz, 'key')
                        this.changeCollapse()
                    } else {
                        this.$message.error(gradeResponse.errorMessage)
                    }
                })
            },
            resetCondition () {
                this.condition.orderId = ''
                this.condition.studentName = ''
                const end = new Date()
                const start = new Date()
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 3)
                this.condition.when = [start, end]
                this.condition.grade = ''
                this.condition.status = '0'
                this.condition.discount = ''
                this.condition.hasRemark = ''
                this.condition.isDelete = '0'
            },
            feeFilter () {
                return this.orders.filter((row) => {
                    const length = Object.keys(this.gradeInfo).length
                    if (length > 0) {
                        const grade = this.gradeInfo[row.grade].value
                        const status = this.statusFormatter(row)
                        return row.orderId.indexOf(this.filter) !== -1 ||
                                row.studentName.indexOf(this.filter) !== -1 ||
                                grade.indexOf(this.filter) !== -1 ||
                                row.realAmount.indexOf(this.filter) !== -1 ||
                                row.totalAmount.indexOf(this.filter) !== -1 ||
                                row.remark.indexOf(this.filter) !== -1 ||
                                status.indexOf(this.filter) !== -1
                    } else {
                        return this.orders
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
            gradeFormatter (row) {
                const name = this.gradeInfo[row.grade].value
                return name
            },
            amountFormatter (row, column) {
                const amount = +row[column.property + 'Amount']
                return '¥' + amount.toLocaleString() + '元'
            },
            operatorFormatter (row, column) {
                let name = row[column.property + 'User']
                let time = row[column.property + 'Time']
                time = this.$moment(time * 1000).format('YYYY-MM-DD HH:mm:ss')
                return name + '<' + time + '>'
            },
            statusFormatter (row) {
                const status = row.status
                return status === 0 ? '收费成功' : '已经退款'
            },
            invoiceFormatter (row) {
                const invoice = +row.invoice
                return invoice === 1 ? '是' : '否'
            },
            feeTypeFormatter (row) {
                const key = row.type
                return this.clazz[key].value
            },
            detailAmountFormatter (row) {
                return '¥' + row.amount.toLocaleString() + '元'
            },
            showUpdateDialog (row) {
                this.currentItem = row
                this.updateDialogTitle = row.studentName + '收费信息修改'
                const feeDetail = JSON.parse(row.feeDetail)
                this.updateFee.fees = _.map(feeDetail, 'id')
                this.updateFee.total = row.totalAmount
                this.updateFee.real = row.realAmount
                this.updateFee.remark = row.remark
                const parameters = {
                    grade: row.grade,
                    status: '0'
                }
                this.isLoadingFeeDetail = true
                this.feeDetailList = []
                axios.post(service.feeSearch, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        responseBody.body.map((item) => {
                            let tmpInfo = item.name.split('_')
                            let title = this.clazz[tmpInfo[1]].value
                            let label = item.key + '[' + title + ']'
                            this.feeDetailList.push({
                                key: item.id,
                                label: label,
                                fee: +item.value,
                                month: item.key
                            })
                        })
                        this.isLoadingFeeDetail = false
                        this.isShowUpdateDialog = true
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            printOnce (row) {
                const parameters = {
                    id: row.id,
                }
                axios.post(service.incomePrintOnce, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('已经发送至打印队列中.')
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            disable (row) {
                const parameters = {
                    id: row.id,
                    orderId: row.orderId,
                    update: {
                        isDelete: row.isDelete == 0 ? 1 : 0
                    }
                }
                axios.post(service.incomeUpdate, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('修改成功')
                        this.search()
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            },
            confirmUpdateIncome () {
                const parameters = {
                    orderId: this.currentItem.orderId,
                    update: this.updateFee,
                    tag: 'feeUpdate',
                    grade: this.currentItem.grade,
                }
                axios.post(service.incomeUpdate, parameters).then((response) => {
                    const responseBody = response.data
                    if (-responseBody.errorCode === 0) {
                        this.$message.success('修改成功')
                    } else {
                        this.$message.error(responseBody.errorMessage)
                    }
                })
            }
        },
        watch: {
            'updateFee.fees': function(newValue, oldValue) {
                this.updateFee.real = ''
                this.updateFee.total = ''
                if (newValue.length > 0) {
                    this.updateFee.real = _.sumBy(this.feeDetailList.filter((current) => {
                        return newValue.indexOf(current.key) !== -1
                    }), 'fee')
                    this.updateFee.total = this.updateFee.real.toLocaleString()
                }
            },
            'updateFee.real': function(newValue) {
                let total = +this.updateFee.total.replace(/,/g, '')
                if (this.updateFee.real !== total) {
                    this.feeTips = '实际收款金额与应收金额有差异请确认已备注好原因，请认真核对收款金额，并且确认已经收款~'
                } else {
                    this.feeTips = '请认真核对收款金额，并且确认已经收款~'
                }
            }
        }
    }
</script>
