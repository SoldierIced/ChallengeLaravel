<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function response($entities = [], $status = 'success')
    {
        if ($status == "success") {
            $code = 200;
        } else {
            $code = 401;
        }
        return response()->json(
            [
                'status' => $status,
                'response' => $entities
            ],
            $code
        );
    }
}
