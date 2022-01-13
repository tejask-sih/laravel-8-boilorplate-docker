<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use DB;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\Admin\States;
use App\Services\V1\Admin\StatesService;

class StateController extends BaseController
{
    protected $statesServices;
    protected $rule, $message,$result = [];
    public function __construct(StatesService $statesServices,Request $request)
    {   
        $this->statesServices = $statesServices;
        $result['status'] = array();
        $this->init($request->route('id'));
    }

    /**
    * Created by Rahul 06/01/2022
    * This Method user for common Rules And Messages
    */
    public function init($id = null)
    {        
        $this->rules = (object) [
            'name' => 'required|unique:mst_states,name,'.$id.'|min:3|max:50|',            
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' =>  __('api.states.NAME_DUPLICATED'),
            'state_name_min' =>  __('api.common.NAME_MIN_LENGTH_ERROR'),
            'state_name_max' =>  __('api.common.NAME_MAX_LENGTH_ERROR'),
        ];        
    }

    /** @OA\Get(
    *    path="/admin/states/list",
    *    tags={"Admin"},
    *    summary="list",
    *    operationId="list",
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
    * Created by Rahul 06/01/2022
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#1#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                //$perPage = '5';
                $paginator = $this->statesServices->list(
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
    *    path="/admin/states/new",
    *    tags={"Admin"},
    *    summary="new states",
    *    operationId="state",
    *
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
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
    * Created by Rahul 06/01/2022        
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */  

    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();           
            $haspermission = $this->checkPrivilege($user_data,'#2#');

             //pr($haspermission);

            if(!empty($haspermission) && $haspermission == 'Yes'){
                $rules = [
                    'name' => $this->rules->name,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.unique'  => $this->message->name_exists,
                    'name.min'  => $this->message->state_name_min,
                    'name.max'  => $this->message->state_name_max,
                ];
                
                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);               
                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {
                    $attributes = $this->attributes($request->all());
                    //pr($attributes);
                    DB::transaction(function() use ($attributes){
                        $stateCreated = $this->statesServices->create($attributes);
                        $this->auditLog('State Created');
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
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/states/edit/{id}",
    *    tags={"Admin"},
    *    summary="update states",
    *    operationId="update",
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
    * Created by Rahul 06/01/2022        
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request,$id)
    {
        try{
            //pr($id);
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#3#');
           // pr($haspermission);
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkState = States::where("id", $id)->first(); // using from Trait Class method
                if(!$checkState){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
                } else {
                    $rules = [
                        'name' => $this->rules->name,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.unique'  => $this->message->name_exists,
                        'name.min'  => $this->message->state_name_min,
                        'name.max'  => $this->message->state_name_max,
                    ];  
                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);
                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        DB::transaction(function() use ($attributes,$id){
                            $this->statesServices->update($attributes,$id);
                            $this->auditLog('State Updated');
                        });
                        $response = $this->setResponse('SUCCESS',[__('api.common.UPDATED')]);
                    }
                }
            }else{
               $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]); 
            }
        }catch(Exception $e)
        {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }

        return send_response($request,$response);
    }

    /**
    * @OA\Post(
    *    path="/admin/states/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete states",
    *    operationId="destroy",    
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
    * Created by Rahul 06/01/2022
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */

    public function destroy(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#5#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkState = States::where("id", $id)->first(); // using from Trait Class method
                if(!$checkState){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->statesServices->delete($id);
                        $this->auditLog('State Deleted');
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
    *    path="/admin/states/activate/{id}",
    *    tags={"Admin"},
    *    summary="change states status",
    *    operationId="activate",    
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
    * Created by Rahul 06/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#4#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkState = States::where("id", $id)->first();
                if(!$checkState){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->statesServices->activate($attributes,$id);
                        $this->auditLog('State Activated');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.ACTIVATED')]);
                }
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.NO_PERMISSION')]);
            }

        }catch(Exception $e)
        {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }

        return send_response($request,$response);
    }

    /**
    * @OA\Post(
    *    path="/admin/states/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change states status",
    *    operationId="deactivate",    
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
    * Created by Rahul 06/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#4#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkState = States::where("id", $id)->first();
                if(!$checkState){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->statesServices->deactivate($attributes,$id);
                        $this->auditLog('State Deactivated');
                    });
                    $response = $this->setResponse('SUCCESS',[__('api.common.DEACTIVATED')]);
                }
            }else{
                $response = $this->setResponse('PERMISSION',['NO_PERMISSION']);
            }
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response);
    }

    /**
    * @OA\Get(
    *    path="/admin/states/lov",
    *    tags={"Admin"},
    *    summary="get state record",
    *    operationId="states_getStatesRecord", 
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
    * Created by Rahul 06/01/2022   
    * @return \Illuminate\Http\Response
    */
    public function getStatesRecord(Request $request)
    {
        try{
            $states = $this->statesServices->getStateList(['id','name']);            
            $response = $this->setResponse('SUCCESS', [''], ['states'=> $states]);
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }
    
}
