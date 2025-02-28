<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'Request was successful.',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data, $message = null, $code = 400)
    {
        return response()->json([
            'status' => 'Request failed.',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    // protected function success($data, $message = null, $code = 200)
    // {
    //     return response()->json([
    //         'status' => 'Request was successful.',
    //         'message' => $message,
    //         'data' => $data,
    //     ], $code);
    // }

    // protected function error($data, $message, $code)
    // {
    //     return response()->json([
    //         'status' => 'Error has occured...',
    //         'message' => $message,
    //         'data' => $data,
    //     ], $code);
    // }
}