<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function sendBadRequestResponse($errors, $message = 'Invalid user request')
    {
        return response()->json([
            "message"=>$message,
            "errors"=>$errors,
            "status"=>0,
            "status_code"=>400,
        ],400);
    }

    protected function sendSuccessResponse($message,$data=[])
    {
        $response = [
            "message"=>$message,
            "status"=>1,
            "status_code"=>200,
        ];
        if($data)
            $response["data"] = $data;

        return response()->json($response,200);
    }

    protected function sendNotFoundResponse($message,$data=[])
    {
        $response = [
            "message"=>$message,
            "status"=>0,
            "status_code"=>404,
        ];
        if(count($data))
            $response["data"] = $data;

        return response()->json($response,404);
    }

    protected function sendUnAuthorisedResponse($message="Unauthorised request")
    {
        $response = [
            "message"=>$message,
            "status"=>0,
            "status_code"=>401,
        ];
        return response()->json($response,401);
    }

    protected function sendGenericErrorResponse($message)
    {
        $response = [
            "message"=>$message,
            "status"=>0,
            "status_code"=>400,
        ];

        return response()->json($response,400);
    }

    protected function sendHttpStatusCode($response,$status_code)
    {
        return response()->json($response,$status_code);
    }
}