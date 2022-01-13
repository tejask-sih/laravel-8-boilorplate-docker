<?php
use Dingo\Api\Routing\Router;
use App\Http\Controllers\V1\AuthController;
/* Admin Folder*/
use App\Http\Controllers\V1\Admin\AreaController;
use App\Http\Controllers\V1\Admin\StateController;
use App\Http\Controllers\V1\Admin\CityController;
use App\Http\Controllers\V1\Admin\InquiryAreaController;
use App\Http\Controllers\V1\Admin\PremisesTypeController;
use App\Http\Controllers\V1\Admin\LocationController;
use App\Http\Controllers\V1\Admin\PremisesController;

$api = app('Dingo\Api\Routing\Router');
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


        $api->group(['prefix' => 'admin'], function ($api) {  
            /* states */
            $api->group(['middleware' => ['jwt.auth'],'prefix' => 'states'], function ($api) {                
                $api->get('list', [StateController::class,'list']);                  
                $api->post('new', [StateController::class,'new']);                  
                $api->post('edit/{id}', [StateController::class,'update']); 
                $api->post('destroy/{id}', [StateController::class,'destroy']);  
                $api->post('activate/{id}',[StateController::class,'activate']);
                $api->post('deactivate/{id}',[StateController::class,'deactivate']); 
                /* record get on all states */  
                $api->get('lov',[StateController::class,'getStatesRecord']);              
            });
            /* cities */
            $api->group(['middleware' => ['jwt.auth'],'prefix' => 'cities'], function ($api) {  
                $api->get('list', [CityController::class,'list']);                  
                $api->post('new', [CityController::class,'new']); 
                $api->post('edit/{id}', [CityController::class,'update']); 
                $api->post('destroy/{id}', [CityController::class,'destroy']);
                $api->post('activate/{id}',[CityController::class,'activate']);
                $api->post('deactivate/{id}',[CityController::class,'deactivate']);  
                /* record get on state wise */    
                $api->get('lov_by_state/{state_id}',[CityController::class,'lov_by_state']);  

            });
            /* area */
            $api->group(['middleware' => ['jwt.auth'],'prefix' => 'area'], function ($api) {                 
                $api->get('list', [AreaController::class,'list']);                 
                $api->post('new', [AreaController::class,'new']);  
                $api->post('edit/{id}', [AreaController::class,'update']);  
                $api->post('destroy/{id}', [AreaController::class,'destroy']);
                $api->post('activate/{id}',[AreaController::class,'activate']);
                $api->post('deactivate/{id}',[AreaController::class,'deactivate']); 
                /* record get on state wise */    
                $api->get('lov_by_city/{city_id}',[AreaController::class,'lov']);
            });
            /* inquiry_area */
            $api->group(['middleware' => ['jwt.auth'],'prefix' => 'inquiry_area'], function ($api) {                                 
                $api->get('list', [InquiryAreaController::class,'list']); 
                $api->post('new', [InquiryAreaController::class,'new']); 
                $api->post('edit/{id}', [InquiryAreaController::class,'update']);  
                $api->post('destroy/{id}', [InquiryAreaController::class,'destroy']);
                $api->post('activate/{id}',[InquiryAreaController::class,'activate']);
                $api->post('deactivate/{id}',[InquiryAreaController::class,'deactivate']);
                $api->get('lov_by_location/{location_id}', [InquiryAreaController::class,'lov_by_location']);
            });

            /* locations */
            $api->group(['middleware' => ['jwt.auth'],'prefix' => 'location'], function ($api) {                                 
                $api->get('list', [LocationController::class,'list']);  
                $api->post('new', [LocationController::class,'new']);   
                $api->post('edit/{id}', [LocationController::class,'update']); 
                $api->post('destroy/{id}', [LocationController::class,'destroy']);  
                $api->post('activate/{id}', [LocationController::class,'activate']);  
                $api->post('deactivate/{id}', [LocationController::class,'deactivate']);
                $api->get('lov/{id}', [LocationController::class,'lov']); 
                $api->get('lov', [LocationController::class,'lov']);               
            });
            /* premises_type */
            $api->group(['prefix' => 'premises_type','middleware' => ['jwt.auth']], function ($api) {               
                $api->get('list', [PremisesTypeController::class,'list']);
                $api->post('new', [PremisesTypeController::class,'new']);
                $api->post('edit/{id}', [PremisesTypeController::class,'update']);
                $api->post('destroy/{id}', [PremisesTypeController::class,'destroy']);
                $api->post('activate/{id}',[PremisesTypeController::class,'activate']);
                $api->post('deactivate/{id}',[PremisesTypeController::class,'deactivate']);
                $api->get('lov',[PremisesTypeController::class,'lov']);
               
            });

            /* premises */
            $api->group(['prefix' => 'premises','middleware' => ['jwt.auth']], function ($api) {               
                $api->get('list', [PremisesController::class,'list']);
                $api->post('new', [PremisesController::class,'new']);
                $api->post('edit/{id}', [PremisesController::class,'update']);
                $api->post('destroy/{id}', [PremisesController::class,'destroy']);
                $api->post('activate/{id}',[PremisesController::class,'activate']);
                $api->post('deactivate/{id}',[PremisesController::class,'deactivate']);
                $api->get('lov/{id}',[PremisesController::class,'lov']);
               
            });

        });
    });


});


?>