<?php

namespace App\Http\Controllers;

use App\Http\Flamingo\Controller;
use App\Http\Flamingo\Token;
use Illuminate\Http\Request;

class User extends Controller{

    public function login(Request $request){
        $userName = $request->input('username');
        $password = $request->input('password');
        if ( ! empty($userName) && ! empty($password)) {
            $users = config('users');
            if (array_key_exists($userName, $users)) {
                if ($password != $users[$userName]['password']) {
                    return response('403 Forbidden, password is error.', 403);
                }
            } else {
                return response('403 Forbidden, user name is not allowed.', 403);
            }
        }
        return $this->toApiMessage();
    }

    public function info(Request $request){
        $requestToken = $request->header('logintoken');
        if ( ! empty($requestToken)) {
            $info = Token::getUserInfoByToken($requestToken);
            $info = [
                'systemName' => config('app.name'),
                'name' => $info['name'],
            ];
        } else {
            $info = [];
        }
        return $this->toApiMessage($info);
    }
}
