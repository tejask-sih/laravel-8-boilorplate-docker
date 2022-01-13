<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use App\Models\Admin\Cities;
use App\Services\V1\Admin\CitiesService;

class CityController extends BaseController
{
    protected $cityService;
    protected $rule, $message;

    public function __construct(CitiesService $cityService,Request $request)
    {
        $this->cityService = $cityService;
        $this->init($request->route('id'));
    }

    /**
    * Created by Rahul 05/01/2022 
    * This Method user for common Rules And Messages
    */   
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_cities,name,'.$id.'|min:3|max:50|',
            'state_id' => 'required',
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' => 'Name Already exist.',
            'city_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'city_name_max' => __('api.common.NAME_MAX_LENGTH_ERROR'),
            'state_id_required' => 'State is not selected',
        ];
    }
    
    /** @OA\Get(
    *    path="/admin/cities/list",
    *    tags={"Admin"},
    *    summary="cities list",
    *    operationId="cities_list",
    *
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}
    *)
    */
    /**
    * Display a listing of the resource.
    * Created by Rahul 05/01/2022
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#6#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->cityService->list(
                    $request,
                    $page,
                    $perPage,
                    $request->filter
                );
                $response = $this->setResponse('SUCCESS',[''],['list' => $paginator]);
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response);
    }

    /**
    * @OA\Post(
    *    path="/admin/cities/new",
    *    tags={"Admin"},
    *    summary=" new city",
    *    operationId="new_city",
    *
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="state_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */ 
    /**  
    * Store a new created Action type in storage.
    * Created by Rahul 05/01/2022        
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */

    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#7#');
            if(!empty($haspermission) && $haspermission == 'Yes'){                
                $rules = [
                    'name' => $this->rules->name,
                    'state_id' => $this->rules->state_id,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.unique'  => $this->message->name_exists,
                    'name.min'  => $this->message->city_name_min,
                    'name.max'  => $this->message->city_name_max,
                    'state_id.required'  => $this->message->state_id_required,
                ];

                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);

                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {                    
                    // pr($request->all());
                    $attributes = $this->attributes($request->all());
                    DB::transaction(function() use ($attributes){
                        $this->cityService->create($attributes);
                        $this->auditLog('City Created');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.CREATED')]);
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response);   
    }

    /**
    * This Method use for manage requested atatribute for Create and Update 
    */
    private function attributes($request)
    {
        $request = (object) $request;
        $attributes['name'] = $request->name;
        $attributes['state_id'] = $request->state_id;
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/cities/edit/{id}",
    *    tags={"Admin"},
    *    summary="update city",
    *    operationId="update_city",
    *
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",               
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="state_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */ 
    /**  
    * Update the specified resource in storage
    * Created by Rahul 05/01/2022         
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#8#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkCity = Cities::where("id", $id)->first(); // using from Trait Class method
                if(!$checkCity){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_CITY_ID')]);
                } else {

                    $rules = [
                        'name' => $this->rules->name,
                        'state_id' => $this->rules->state_id,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.unique'  => $this->message->name_exists,
                        'name.min'  => $this->message->city_name_min,
                        'name.max'  => $this->message->city_name_max,
                        'state_id.required'  => $this->message->state_id_required,
                    ];

                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);

                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        DB::transaction(function() use ($attributes,$id){
                            $this->cityService->update($attributes,$id);
                            $this->auditLog('City Updated');
                        });
                        $response = $this->setResponse('SUCCESS',[__('api.common.UPDATED')]);
                    }
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response);
    }

    /**
    * @OA\Post(
    *    path="/admin/cities/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete city",
    *    operationId="destroy_city",    
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */

    /**
    * Remove the specified resource from storage.
    * Created by Rahul 05/01/2022
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#10#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkCity = Cities::where("id", $id)->first(); // using from Trait Class method
                if(!$checkCity){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_CITY_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->cityService->delete($id);
                        $this->auditUserLog('City Deleted');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.DELETED')]);
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response);           
    }

    /**
    * @OA\Post(
    *    path="/admin/cities/activate/{id}",
    *    tags={"Admin"},
    *    summary="change city status",
    *    operationId="activate_city",    
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */
    /**
    * Activate the specified resource from storage.
    * Created by Rahul 05/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#9#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkCity = Cities::where("id", $id)->first(); // using from Trait Class method
                if(!$checkCity){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_CITY_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->cityService->activate($attributes,$id);
                        $this->auditUserLog('City Activated');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.ACTIVATED')]);
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

    /**
    * @OA\Post(
    *    path="/admin/cities/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change city status",
    *    operationId="deactivate_city",    
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */    
    /**
    * Deactivate the specified resource from storage.
    * Created by Rahul 05/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#1#');
            
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkCity = Cities::where("id", $id)->first(); // using from Trait Class method
                if(!$checkCity){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_CITY_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->cityService->deactivate($attributes,$id);
                        $this->auditLog('City Deactivated');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.DEACTIVATED')]);
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

    /**
    * @OA\Get(
    *    path="/admin/cities/lov_by_state/{state_id}",
    *    tags={"Admin"},
    *    summary="get city record",
    *    operationId="lov_by_state",    
    *    @OA\Parameter(
    *        name="state_id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),    
    *    @OA\Response(
    *        response=200,
    *        description="Success",
    *        @OA\MediaType(
    *            mediaType="application/json",
    *        )
    *    ),
    *    @OA\Response(
    *        response=401,
    *        description="Unauthorized"
    *    ),
    *    @OA\Response(
    *        response=400,
    *        description="Invalid request"
    *    ),
    *    @OA\Response(
    *        response=403,
    *        description="Unauthorized Access"
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */
    /**
    * Get the specified resource from storage.
    * Created by Rahul 05/01/2022
    * @param  \App\State  $state_id
    * @return \Illuminate\Http\Response
    */
    public function lov_by_state(Request $request,$state_id="")
    {
        try{ 
            $checkState = DB::table('mst_states')->where("id", $state_id)->first();
            if(!empty($state_id) &&  $checkState)
            {
                $cities = $this->cityService->getCityList($state_id,['id','name']);
                $response = $this->setResponse('SUCCESS', [''], ['cities'=> $cities]);
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
            }            
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }


}
