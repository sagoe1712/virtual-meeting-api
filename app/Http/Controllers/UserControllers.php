<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserControllers extends Controller
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
            "status" => "nullable|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors(), $validator->errors()->first());
        }
        //check if email exists
        $check_user = User::where('email', $request->email)->first();

            if($check_user){

                return $this->sendBadRequestResponse([], "Email already exists");
            }
        
        $hash_password = Hash::make($request->password);
        $addUser = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'slug' => trim($request->first_name . "-" . Str::random(5)),
            'password' =>  $hash_password,
            'status' => 1
        ]);

        
        if ($addUser) {

            /**Take note of this: Your user authentication access token is generated here **/
        $data['token'] =  $addUser->createToken('MyApp')->accessToken;
        $data['name'] =  $addUser->first_name;
            return $this->sendSuccessResponse("Success", $data);
        } else {
            return $this->sendBadRequestResponse("Unable to register admin user");
        }
    }

    //

    public function view_profile(Request $request){

        return $request->user();
    }

    public function user_details(Request $request){

        if($request->user_id){
        $user_id = $request->user_id;

            $get_data = User::find($user_id);

            if($get_data){

                return $this->sendSuccessResponse("Success", $get_data);
            } else {

                return $this->sendBadRequestResponse("Error getting user details");
            }
        } else{

            return $this->sendBadRequestResponse("Error getting user");
        }
    }
    
}