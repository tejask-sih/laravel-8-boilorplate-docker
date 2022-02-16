<?php
use Dingo\Api\Routing\Router;
use App\Http\Controllers\V1\AuthController;
/* Admin Folder*/


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function (Router $api) {
    $api->get('/', function () {
        echo 'Welcome to project';
    });
});




$api->version('v1',['middleware' => ['api']], function (Router $api) {

    $api->group(['prefix' => 'v1'], function ($api) {
        $api->group(['prefix' => 'auth'], function ($api) { 
            $api->post('init', [AuthController::class,'init']);
            $api->post('login', [AuthController::class,'login']);          
            $api->post('register', [AuthController::class,'register']);          
            $api->post('forgot-password', [AuthController::class,'forgotPassword']);          
            $api->post('reset-password', [AuthController::class,'resetPassword']);          
            
            /* logged after handle it*/
            $api->group(['middleware' => ['jwt.auth']], function ($api) {                          
                $api->get('logout', [AuthController::class,'logout']);  
                $api->get('me', [AuthController::class,'me']); 
                $api->post('change-password', [AuthController::class,'changepassword']); 
                $api->get('list-user', [AuthController::class,'listuser']);                       
                $api->get('user-delete/{id}', [AuthController::class,'deleteuser']);                       
            });
            /* /logged after handle it*/
        });



    });


});


?>