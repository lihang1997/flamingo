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
                            clearable
                            :picker-options="pickerOptions">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model='condition.grade' clearable placeholder='选择年级(可不选)'>
                            <el-option 
                            v-for='item in grade'
                            :key='item.key'
                            :value='item.key'
                            :label='item.value'>
                            </el-option>
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
            <el-dialog
            title='添加费用明细'
            :visible.sync='isShowFeeAdd'
            width="60%">
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
                        clearable
                        :picker-options="pickerOptions">
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
                        <el-col :span='5' style='margin-right: 8px;'
                            v-for='(classOne, idxClass) in clazz' 
                            :index='idxClass'
                            :key='idxClass'
                            :value='idxClass'>
                                <el-input
                                v-model="feeAdd.fees[item + '_' + classOne.key]"
                                @input='forceUpdate'
                                autofocus
                                :placeholder="'填写' + classOne.value + '费用.'">
                                    <template slot="append">元</template>
                                </el-input>
                        </el-col>
                    </el-row>
                </el-form-item>
            </el-form>
            <div slot='footer'>
                <el-button size='small' plain @click='isShowFeeAdd = false'>取 消</el-button>
                <el-button size='small' type='success' plain >提 交</el-button>
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
                hasInit: false,
                pickerOptions: {
                    shortcuts: [{
                        text: '本月',
                        onClick(picker) {
                            picker.$emit('pick', [new Date(), new Date()])
                        }
                    }, {
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
                            start.setMonth(start.getMonth() - 6)
                            picker.$emit('pick', [start, end])
                        }
                    }, {
                        text: '最近十二个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setMonth(start.getMonth() - 12)
                            picker.$emit('pick', [start, end])
                        }
                    }]
                },
                condition: {
                    when: [],
                    grade: '',
                },
                feeAdd: {
                    when: [],
                    grade: [],
                    fees: {},
                },
                isShowFeeAdd: false,
            }
        },
        created () {
            const end = new Date();
            const start = new Date();
            start.setMonth(start.getMonth() - 12)
            this.condition.when = [start, end]

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
                gradeResponse.body.forEach((gradeOne) => {
                    classResponse.body.forEach((classOne) => {
                        let key = gradeOne.key + '_' + classOne.key
                        this.feeAdd.fees[key] = ''
                    })
                })
                this.hasInit = true
            })
        },
        methods: {
            search () {

            },
            forceUpdate () {
                this.hasInit = false
                this.$nextTick(() => {
                    this.hasInit = true
                })
            }
        },
    }
</script>
