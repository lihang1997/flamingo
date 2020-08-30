<?php

namespace App\Http\Flamingo;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\DB;

class Model extends BaseModel{

    const STATUS_OK = 0;
    const STATUS_DISABLE = 1;

    protected $table = '';

    public function getList(array $where, $select = '*'){
        !is_array($select) && $select = [$select];
        $query = DB::table($this->table);
        $query = call_user_func_array([$query, 'select'], $select);
        $query = $query->where($where);
        $result = $query->get()->toArray();
        return $result;
    }

    public function insert($row){
        $result = DB::table($this->table)->insert($row);
        return $result;
    }

    public function switchTable($table){
        if ( ! empty($table) && is_string($table)) {
            $this->table = $table;
        }
    }

    protected function getDao(){
        return DB::table($this->table);
    }
}
