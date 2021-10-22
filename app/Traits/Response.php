<?php

namespace App\Traits;


trait Response
{
    public function success($msg)
    {
        return response()->json([
            'success' => true,
            'msg' => $msg
        ], 200);
    }

    public function error($msg)
    {
        return response()->json([
            'success' => false,
            'msg' => $msg
        ], 400);
    }

    public function with_cookie($msg, $cookie)
    {
        return response([
            'msg' => $msg
        ])->withCookie($cookie);
    }
}
