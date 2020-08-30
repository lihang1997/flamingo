<?php

namespace App\Http\Models;

use App\Http\Flamingo\Model;

class Config extends Model{
    
    protected $table = 'config';

    public function getConfigList($group, $name){
        $select = [
            'key',
            'value',
        ];
        $where = [
            'group' => $group,
            'name' => $name,
            'status' => self::STATUS_OK,
        ];
        return $this->getList($where, $select);
    }

    public function getGradeList(){
        return $this->getConfigList('system', 'grade');
    }

    public function getClassList(){
        return $this->getConfigList('system', 'class');
    }

    public function getFeeDetail($month, $key) {
        $select = ['id'];
        $where = [
            'group' => 'fee',
            'name' => $key,
            'key' => $month,
        ];
        return $this->getList($where, $select);
    }

    public function addOne($month, $key, $value, $userName){
        $time = time();
        $insert = [
            'group' => 'fee',
            'name' => $key,
            'key' => $month,
            'value' => $value,
            'createUser' => $userName,
            'createTime' => $time,
            'updateUser' => $userName,
            'updateTime' => $time,
        ];
        $this->getDao()->insert($insert);
    }

    public function updateOne($month, $key, $value, $userName){
        $update = [
            'value' => $value,
            'updateUser' => $userName,
            'updateTime' => time(),
        ];
        $this->getDao()
        ->where('group', '=', 'fee')
        ->where('name', '=', $key)
        ->where('key', '=', $month)
        ->update($update);
    }

    public function feeSearch($start, $end, $grade, $status){
        $dao = $this->getDao();
        $dao->where('group', '=', 'fee');
        if ( ! empty($start)) {
            $dao->where('key', '>=', date('Ym', $start));
        }
        if ( ! empty($end)) {
            $dao->where('key', '<=', date('Ym', $end));
        }
        if ($status !== null) {
            $dao->where('status', '=', +$status);
        }
        $result = $dao->get()->toArray();
        if ( ! empty($result)) {
            if( ! empty($grade)) {
                $result = array_filter($result, function($item) use ($grade){
                    $name = $item->name;
                    return preg_match('/^' . $grade . '/', $name);
                });
            }
        } else {
            $result = [];
        }
        return $result;
    }

    public function feeUpdate($id, $update, $loginName){
        if ( ! empty($update)) {
            $update['updateUser'] = $loginName;
            $update['updateTime'] = time();
            $dao = $this->getDao();
            $dao->where('id', '=', +$id)->update($update);
        }
    }
}
