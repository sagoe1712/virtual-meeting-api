<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|email",
            "password" => "required|string|min:6",
            "phone" => "nullable|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors(), $validator->errors()->first());
        }
        //check if email exists
        $check_user = Admin::where('email', $request->email)->first();

        if ($check_user) {

            return $this->sendBadRequestResponse([], "Email already exists");
        }
        $addUser = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            //'slug' => trim($request->first_name . "-" . Str::random(5)),
            'password' =>  Hash::make($request->password),
            'status' => 1
        ]);




        if ($addUser) {

            $data['token'] =  $addUser->createToken('MyApp')->accessToken;
            $data['name'] =  $addUser->first_name;
            return $this->sendSuccessResponse("Success", $data);
        } else {
            return $this->sendBadRequestResponse("Unable to register admin user");
        }
    }


    public function login(Request $request)
    {
        //if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',




        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors(), 'Validation Error - ' . $validator->errors()->first());
        }
        //  $user = Auth::user();
        $user = Admin::where(['email' => $request->email])->first();
        if ($user == null) {
            return $this->sendBadRequestResponse(['error' => 'Unauthorised'], 'Invalid email and password combination.');
        }
        if (Hash::check($request->password, $user->password)) {
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->first_name;


            return $this->sendSuccessResponse($success, 'User login successfully.');
        } else {
            return $this->sendBadRequestResponse(['error' => 'Unauthorised'], 'Invalid email and password combination');
        }
    }

    public function view_admin_profile(Request $request)
    {

        //dd($request);
        return Auth::user()->first_name;
    }

    public function log_out()
    {
        $user = Auth::user()->token();
        $user->revoke();

        return $this->sendSuccessResponse("log out", 'Success.');
    }
}