<?php

namespace App\Http\Controllers;

use App\Http\Flamingo\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Students as ServiceStudents;
use App\Http\Flamingo\Token;

class Students extends Controller{

    public function search(Request $request){
        $name = $request->get('name');
        $service = new ServiceStudents();
        $students = $service->getStudentList($name);
        return $this->toApiMessage($students);
    }

    public function create(Request $request){
        $post = $request->all();
        $service = new ServiceStudents();
        $student = $service->getByName($post['name']);
        if ( ! empty($student)) {
            return $this->toApiMessage(NULL, -1, '学生信息已经存在，请不要重复添加.');
        }
        $result = $service->addOne($post, Token::$loginName);
        if ($result) {
            return $this->toApiMessage();
        } else {
            return $this->toApiMessage(NULL, -1, '数据写入失败.');
        }
    }

    public function update(Request $request){
        $post = $request->all();
        $service = new ServiceStudents();
        $result = $service->updateStudent($post, Token::$loginName);
        if ($result) {
            return $this->toApiMessage();
        } else {
            return $this->toApiMessage(NULL, -1, '数据写入失败.');
        }
    }
}
