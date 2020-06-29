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
}
