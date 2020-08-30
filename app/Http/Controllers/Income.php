<?php

namespace App\Http\Controllers;

use App\Http\Flamingo\Controller;
use Illuminate\Http\Request;
use App\Http\Flamingo\Token;
use App\Http\Models\Config as ConfigService;
use App\Http\Models\Income as IncomeService;

class Income extends Controller{

    use Token;

    private $model = null;

    public function __construct(){
        $this->model = new IncomeService();
    }

    public function add(Request $request){
        $all = $request->all();
        $student = $all['student'];
        $income = $all['income'];
        $other = $all['other'];
        $feeDetail = $this->getFeeDetail($student['grade'], $income['fees']);
        $orderId = $this->makeOrderId();
        $invoiceInfo = $this->makeInvoiceSource($student, $income, $feeDetail, $orderId, $other);
        $result = $this->insertIncomeExtra($feeDetail, $orderId);
        if ($result) {
            $result = $this->insertIncome($student, $income, $feeDetail, $orderId, $invoiceInfo);
            if ($result && ! empty($income['invoice'])) {
                $html = view('ticket', $invoiceInfo);
                // todo
            }
        }
        if ($result) {
            return $this->toApiMessage();
        } else {
            return $this->toApiMessage(NULL, -1 , '数据写入失败.');
        }
    }

    public function search(Request $request){
        $all = $request->all();
        $condition = $this->makeWhere($all);
        $dao = $this->model->dao(IncomeService::T_INCOME);
        $dao = $dao->where($condition['where']);
        if ( ! empty($condition['whereRaw'])) {
            $dao = $dao->whereRaw($condition['whereRaw']);
        }
        $orders = $dao->get()->toArray();
        $configService = new ConfigService();
        $classList = $configService->getClassList();
        $result = [
            'orders' => $orders,
            'clazz' => $classList,
        ];
        return $this->toApiMessage($result);
    }

    public function update(Request $request){
        $all = $request->all();
        $orderId = $all['orderId'];
        $this->model->addIncomeLog($orderId, $all, Token::$loginName);
        $update = $this->getUpdateInfo($all);
        $update['updateUser'] = Token::$loginName;
        $update['updateTime'] = time();
        $result = $this->model->dao(IncomeService::T_INCOME)->where('orderId', $orderId)->update($update);
        if ($result) {
            if ( ! empty($all['tag']) && $all['tag'] == 'feeUpdate') {
                $feeDetail = json_decode($update['feeDetail'], true);
                $this->insertIncomeExtra($feeDetail, $orderId);
            }
            return $this->toApiMessage();
        } else {
            return $this->toApiMessage(NULL, 1, 'DB error.');
        }
    }

    public function printOnce(Request $request){
        $all = $request->all();
        $dao = $this->model->dao(IncomeService::T_INCOME)->where('id', +$all['id']);
        $result = $dao->get()->toArray();
        if ( ! empty($result['0'])) {
            $info = $result['0']->invoiceDetail;
            // todo
            $update = [
                'invoice' => 1,
                'updateUser' => Token::$loginName,
                'updateTime' => time(),
            ];
            $dao->update($update);
            return $this->toApiMessage();
        } else {
            return $this->toApiMessage(NULL, 1, 'DB error.');
        }
    }

    private function getUpdateInfo($all){
        $update = $all['update'];
        if ( ! empty($all['tag']) && $all['tag'] == 'feeUpdate') {
            $update['feeDetail'] = $this->getFeeDetail($all['grade'], $update['fees']);
            $update['feeDetail'] = json_encode($update['feeDetail']);
            $udpate['realAmount'] = $update['real'];
            $udpate['totalAmount'] = $update['total'];
            unset($update['fees'], $update['real'], $update['total']);
        }
        return $update;
    }

    private function makeWhere($post){
        $where = [];
        $whereRaw = '';
        if ( ! empty($post['orderId'])) {
            $where[] = ['orderId', '=', $post['orderId']];
            return $where;
        }
        if ( ! empty($post['studentName'])) {
            $where[] = ['studentName', '=', $post['studentName']];
        }
        if ( ! empty($post['grade'])) {
            $where[] = ['grade', '=', $post['grade']];
        }
        if ( ! empty($post['when'])) {
            $start = date('Y-m-d 00:00:00', $post['when']['0']);
            $end = date('Y-m-d 23:59:59', $post['when']['1']);
            $where[] = ['createTime', '>=', strtotime($start)];
            $where[] = ['createTime', '<=', strtotime($end)];
        }
        if ( ! empty($post['status'])) {
            $where[] = ['status', '=', +$post['status']];
        } else {
            $where[] = ['status', '=', 0];
        }
        if ( ! empty($post['discount'])) {
            if ($post['discount'] == 1) {
                $whereRaw = 'realAmount<totalAmount';
            } elseif ($post['discount'] == 2) {
                $whereRaw = 'realAmount=totalAmount';
            }
        }
        if ( ! empty($post['hasRemark'])) {
            if ($post['hasRemark'] == 1) {
                $where[] = ['remark', '!=', ''];
            } elseif ($post['hasRemark'] == 2) {
                $where[] = ['remark', '=', ''];
            }
        }
        if ( ! empty($post['isDelete'])) {
            $where[] = ['isDelete', '=', 1];
        } else {
            $where[] = ['isDelete', '=', 0];
        }
        $result = [
            'where' => $where,
            'whereRaw' => $whereRaw,
        ];
        return $result;
    }

    private function makeInvoiceSource($student, $income, $feeDetail, $orderId, $other){
        $configService = new ConfigService();
        $classList = $configService->getClassList();
        $classList = array_column($classList, NULL, 'key');
        $invoiceInfo = [
            'orderId' => $orderId,
            'studentName' => $student['name'],
            'grade' => $other['gradeName'],
            'totalAmount' => $income['total'],
            'realAmount' => $income['real'],
            'feeDetail' => $feeDetail,
            'remark' => $income['remark'],
            'createUser' => Token::$loginName,
            'classList' => $classList,
            'now' => date('Y-m-d H:i:s'),
        ];
        return $invoiceInfo;
    }

    private function insertIncome($student, $income, $feeDetail, $orderId, $invoiceInfo){
        $now = time();
        $order = [
            'orderId' => $orderId,
            'studentName' => $student['name'],
            'grade' => $student['grade'],
            'totalAmount' => str_replace(',', '', $income['total']),
            'realAmount' => str_replace(',', '', $income['real']),
            'remark' => $income['remark'],
            'feeDetail' => json_encode($feeDetail),
            'invoice' => +$income['invoice'],
            'invoiceDetail' => json_encode($invoiceInfo),
            'createUser' => Token::$loginName,
            'createTime' => $now,
            'updateUser' => Token::$loginName,
            'updateTime' => $now,
        ];
        $this->model->switchTable(IncomeService::T_INCOME);
        $result = $this->model->insert($order);
        return $result;
    }

    private function makeSignCode($orderId, $income, $time){
        $hashString = $orderId . $income['total'] . $income['real'] . $time;
        return crc32($hashString);
    }

    private function insertIncomeExtra($feeDetail, $orderId){
        $insert = [];
        $now = time();
        foreach($feeDetail as $item){
            $insert[] = [
                'orderId' => $orderId,
                'feeId' => $item['id'],
                'month' => $item['month'],
                'grade' => $item['grade'],
                'type' => $item['type'],
                'amount' => $item['amount'],
                'createUser' => Token::$loginName,
                'createTime' => $now,
                'updateUser' => Token::$loginName,
                'createTime' => $now,
            ];
        }
        $this->model->switchTable(IncomeService::T_INCOME_EXTRA);
        $this->model->where('orderId', $orderId)->update(['status' => 1]);
        return $this->model->insert($insert);
    }

    private function makeOrderId(){
        $unique = uniqid();
        return $unique;
    }

    private function getFeeDetail($grade, $fees){
        $result = [];
        $configService = new ConfigService();
        $detail = $configService->feeSearch(null, null, $grade, ConfigService::STATUS_OK);
        if ( ! empty($detail) && ! empty($fees)) {
            foreach($detail as $item){
                if (in_array($item->id, $fees)) {
                    list($grade, $type) = explode('_', $item->name);
                    $result[] = [
                        'id' => $item->id,
                        'month' => $item->key,
                        'grade' => $grade,
                        'type' => $type,
                        'amount' => $item->value,
                    ];
                }
            }
        }
        return $result;
    }
}
