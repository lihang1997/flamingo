<?php

namespace App\Http\Flamingo;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Flamingo\Token;
class Controller extends BaseController
{

    use Token;

    protected function toApiMessage($result = NULL, $code = 0, $message = 'Success'){
        $body = [
            'errorCode' => $code,
            'errorMessage' => $message,
        ];
        $result !== NULL && $body['body'] = $result;
        return response()->json($body);
    }
}
