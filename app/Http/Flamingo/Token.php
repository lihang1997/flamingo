<?php

namespace App\Http\Flamingo;

trait Token{
    
    static protected $expire = 30;

    public static function makeToken($request){
        $token = '';
        $ua = $request->userAgent();
        $ip = $request->ip();
        if ( ! empty($ua) && ! empty($ip)) {
            $unique = uniqid();
            $expire = time() + self::$expire;
            $name = $request->input('username');
            $userInfo = [
                'ua' => $ua,
                'ip' => $ip,
                'unique' => $unique,
                'expire' => $expire,
                'name' => $name,
            ];
            $userInfo = json_encode($userInfo);
            $token = encrypt($userInfo);
        }
        return $token;
    }

    public static function getUserInfoByToken($secret, $column = NULL){
        $info = decrypt($secret);
        return json_decode($info, TRUE);
    }

    public static function isEffective($requestToken, $request){
        $result = FALSE;
        $userInfo = self::getUserInfoByToken($requestToken);
        if ( ! empty($userInfo)) {
            $ua = $request->userAgent();
            $ip = $request->ip();
            $result = $ua == $userInfo['ua']
                && $ip == $userInfo['ip']
                && time() < $userInfo['expire'];
            if ($result) {
                $users = config('users');
                $result = array_key_exists($userInfo['name'], $users);
            }
        }
        return $result;
    }
}
