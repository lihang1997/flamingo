<?php

namespace App\Http\Models;

use App\Http\Flamingo\Model;

class Students extends Model{

    protected $table = 'students';

    public function getStudentList($name, $grade, $status){
        $where = [
            ['name', 'like', '%' . $name . '%'],
        ];
        if ( ! empty($grade)) {
            $where[] = ['grade', '=', $grade];
        }
        if (in_array($status, ['0', '1'])) {
            $where[] = ['status', '=', $status];
        }
        $result = $this->getDao()->where($where)->get()->toArray();
        if ( ! is_array($result)) {
            $result = [];
        }
        return $result;
    }

    public function getByName($name){
        $where = ['name' => $name];
        $list = $this->getList($where);
        if ( ! empty($list['0'])) {
            $list = $list['0'];
        }
        return $list;
    }

    public function addOne($info, $loginName){
        $time = time();
        $info['createUser'] = $loginName;
        $info['createTime'] = $time;
        $info['updateUser'] = $loginName;
        $info['updateTime'] = $time;
        $result = $this->insert($info);
        return $result;
    }

    public function updateStudent($update, $loginName){
        $update['updateUser'] = $loginName;
        $update['updateTime'] = time();
        $id = +$update['id'];
        unset($update['id']);
        $result = $this->getDao()->where('id', '=', $id)->update($update);
        return $result;
    }
}
