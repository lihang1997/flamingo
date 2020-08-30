<?php

namespace App\Http\Models;

use App\Http\Flamingo\Model;

class Income extends Model{
    
    const T_INCOME = 'income';
    const T_INCOME_EXTRA = 'incomeExtra';
    const T_INCOME_LOG = 'incomeLog';
    
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;

    public function dao($table){
        return parent::setTable($table)->getDao();
    }

    public function addIncomeLog($orderId, $context, $user){
        !is_string($context) && $context = json_encode($context);
        $record = [
            'orderId' => $orderId,
            'context' => $context,
            'createUser' => $user,
            'createTime' => time(),
        ];
        return $this->dao(self::T_INCOME_LOG)->insert($record);
    }
}
