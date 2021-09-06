<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use App\Models\ClientModel;
use App\Models\User;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sendMsg = new Controller();
        //$header = $request->header('token');
//            if ($request->input('api_token')) {
        $slug = $request->slug;  
        if (!$slug) {
                return $sendMsg->sendBadRequestResponse("Slug required");
            }
            $user_details = $this->getSlug($slug);

            if(!$user_details){
                return $sendMsg->sendBadRequestResponse("Invalid user");
                
            }

            $request->user_id = $user_details->id;
            
        return $next($request);
    }


    private function getSlug($slug)
    {
        return User::where('slug', $slug)->first();
    }
}