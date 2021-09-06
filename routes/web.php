<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'contact/{slug}', 'middleware' => 'access-go'], function () use ($router) {

    $router->get('details', 'UserControllers@user_details');
    $router->get('appointments', 'AppointmentController@get_user_appointments');
    $router->get('channels', 'MeetingController@show_channels');
    $router->post('appointment/create', 'MeetingController@create_meeting_details');

    $router->get('email_test', 'MeetingController@email_test');

});

$router->group(['prefix' => 'user'], function () use ($router) {

    $router->post('create', 'UserControllers@register');

    $router->group(['middleware' => 'auth:api'], function () use ($router) {

        $router->get('profile', 'UserControllers@view_profile');
    });
});


$router->group(['prefix' => 'admin'], function () use ($router) {

    $router->post('create', 'AdminController@register');
    $router->post('login', 'AdminController@login');

    $router->group(['middleware' => ['admin', 'auth:api']], function () use ($router) {

        $router->get('profile', 'AdminController@view_admin_profile');
        $router->get('logout', 'AdminController@log_out');
        $router->post('appointment/create', 'AppointmentController@bulk_create_appointment');
    });
});