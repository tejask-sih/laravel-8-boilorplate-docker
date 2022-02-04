<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use App\Models\Admin\PremisesType;
use App\Services\V1\Admin\PremisesTypeService;

class PremisesTypeController extends BaseController
{
    protected $premisesTypeService;
    protected $rule, $message,$result = [];

    public function __construct(PremisesTypeService $premisesTypeService,Request $request)
    {
        
        $this->premisesTypeService = $premisesTypeService;
        $result['status'] = array();
        $this->init($request->route('id'));
    }
    
    /**
    * Created by Rahul 07/01/2022
    * This Method user for common Rules And Messages
    */
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_premises_types,name,'.$id.'|min:3|max:50|',
            
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' =>  __('api.premise_type.NAME_DUPLICATED'),
            'premises_type_name_min' =>  __('api.common.NAME_MIN_LENGTH_ERROR'),
            'premises_type_name_max' =>  __('api.common.NAME_MAX_LENGTH_ERROR'),
        ];        
    }

    /** @OA\Get(
    *    path="/admin/premises_type/list",
    *    tags={"Admin"},
    *    summary="list",
    *    operationId="premises_type_list",
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
    * Created by Rahul 07/01/2022
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#35#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->premisesTypeService->list(
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
    *    path="/admin/premises_type/new",
    *    tags={"Admin"},
    *    summary=" new",
    *    operationId="premises_type_new",
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
    * Created by Rahul 07/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#36#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $rules = [
                    'name' => $this->rules->name,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.unique'  => $this->message->name_exists,
                    'name.min'  => $this->message->premises_type_name_min,
                    'name.max'  => $this->message->premises_type_name_max,
                ];
                
                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);
                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {
                    $attributes = $this->attributes($request->all());
                    DB::transaction(function() use ($attributes){
                        $stateCreated = $this->premisesTypeService->create($attributes);
                        $this->auditLog('Premise Type Created');
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
    * Created by Rahul 07/01/2022
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
    *    path="/admin/premises_type/edit/{id}",
    *    tags={"Admin"},
    *    summary=" edit",
    *    operationId="premises_type_update",
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
    * Update the specified resource in storage.
    * Created by Rahul 07/01/2022
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#37#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremisesType = PremisesType::where("id", $id)->first(); // using from Trait Class method
                if(!$checkPremisesType){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_TYPE_ID')]);
                } else {
                
                    $rules = [
                        'name' => $this->rules->name,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.unique'  => $this->message->name_exists,
                        'name.min'  => $this->message->premises_type_name_min,
                        'name.max'  => $this->message->premises_type_name_max,
                    ];
                

                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);
                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        DB::transaction(function() use ($attributes,$id){
                            $this->premisesTypeService->update($attributes,$id);
                            $this->auditLog('Premise Type Updated');
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
    *    path="/admin/premises_type/destroy/{id}",
    *    tags={"Admin"},
    *    summary=" destroy",
    *    operationId="premises_type_destroy",    
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
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
    * Remove the specified resource from storage.
    * Created by Rahul 07/01/2022
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#39#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremisesType = PremisesType::where("id", $id)->first(); // using from Trait Class method
                if(!$checkPremisesType){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_TYPE_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->premisesTypeService->delete($id);
                        $this->auditLog('Premise Type Deleted');
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
    *    path="/admin/premises_type/activate/{id}",
    *    tags={"Admin"},
    *    summary="change status",
    *    operationId="premises_type_activate",    
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
    * Created by Rahul 07/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#38#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremisesType = PremisesType::where("id", $id)->first();
                if(!$checkPremisesType){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_TYPE_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->premisesTypeService->activate($attributes,$id);
                        $this->auditLog('Premise Type Activated');
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
    *    path="/admin/premises_type/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change status",
    *    operationId="premises_type_deactivate",    
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
    * Created by Rahul 07/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#39#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremisesType = PremisesType::where("id", $id)->first();
                if(!$checkPremisesType){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_TYPE_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->premisesTypeService->deactivate($attributes,$id);
                        $this->auditLog('Premise Type Deactivated');
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
    *    path="/admin/premises_type/lov",
    *    tags={"Admin"},
    *    summary="get premises type record",
    *    operationId="getStatesRecord", 
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
    * Created by Rahul 07/01/2022   
    * @return \Illuminate\Http\Response
    */
    public function lov(Request $request)
    {
        try{
            $states = $this->premisesTypeService->lov(['id','name']);            
            $response = $this->setResponse('SUCCESS', [''], ['states'=> $states]);
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }
}
