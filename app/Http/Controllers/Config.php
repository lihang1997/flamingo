<?php

namespace App\Http\Controllers;
use App\Http\Flamingo\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Config as ConfigService;

class Config extends Controller{

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
}
