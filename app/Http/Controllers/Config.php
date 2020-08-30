<?php

namespace App\Http\Controllers;
use App\Http\Flamingo\Controller;
use App\Http\Flamingo\Token;
use Illuminate\Http\Request;
use App\Http\Models\Config as ConfigService;

class Config extends Controller{

    use Token;

    public function getByName(Request $request){
        $name = $request->input('name');
        $name = ucfirst($name);
        $service = new ConfigService();
        $function = 'get' . $name .  'List';
        if (is_callable([$service, $function])) {
            $list = $service->$function();
        } else {
            $list = [];
        }
        return $this->toApiMessage($list);
    }

    public function feeSetting(Request $request){
        $post = $request->all();
        $start = $post['when']['0'];
        $end = $post['when']['1'];
        $grade = $post['grade'];
        $fees = $post['fees'];
        if ( ! empty($start) && ! empty($end) && ! empty($grade) && ! empty($fees)) {
            $result = $this->saveFees($start, $end, $grade, $fees);
            $message = '新增了' . $result['insert'] . '个数据;';
            $message .= '更新了' . $result['update'] . '个数据.';
            return $this->toApiMessage(['message' => $message]);
        } else {
            return $this->toApiMessage(NULL, -1, '参数错误.');
        }
    }

    public function feeSearch(Request $request){
        $post = $request->all();
        $start = $post['start'] ?? null;
        $end = $post['end'] ?? null;
        $grade = $post['grade'];
        $status = $post['status'] ?? null;
        $service = new ConfigService();
        $result = $service->feeSearch($start, $end, $grade, $status);
        $list = [];
        foreach ($result as $item) {
            $item = json_decode(json_encode($item), true);
            list($grade, $category) = explode('_', $item['name']);
            $item['grade'] = $grade;
            $item['category'] = $category;
            $list[] = $item;
        }
        return $this->toApiMessage($list);
    }

    public function feeUpdate(Request $request){
        $post = $request->all();
        $id = $post['id'];
        $update = $post['update'];
        $service = new ConfigService();
        $service->feeUpdate($id, $update, Token::$loginName);
        return $this->toApiMessage();
    }

    private function saveFees($start, $end, $grade, $fees) {
        $result = [
            'insert' => 0,
            'update' => 0,
        ];
        $monthList = [];
        $service = new ConfigService();
        for ($time = $start; $time <= $end; $time += 86400) {
            $month = date('Ym', $time);
            if (array_key_exists($month, $monthList)) {
                continue;
            }
            $monthList[$month] = 1;
            foreach ($fees as $key => $value) {
                list($gradeOne) = explode('_', $key);
                if ( ! in_array($gradeOne, $grade)) {
                    continue;
                }
                $fee = $service->getFeeDetail($month, $key);
                if ( ! empty($fee)) {
                    // update
                    $service->updateOne($month, $key, $value, Token::$loginName);
                    $result['update']++;
                } else {
                    // insert
                    $service->addOne($month, $key, $value, Token::$loginName);
                    $result['insert']++;
                }
            }
        }
        return $result;
    }
}
