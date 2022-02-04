<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;
use Validator;
use App\Models\Admin\Area;
use App\Services\V1\Admin\AreaService;
use Exception;
use App\Models\Admin\Cities;


class AreaController extends BaseController
{
    protected $areaService;
    protected $rule, $message;

    public function __construct(AreaService $areaService,Request $request)
    {
        $this->areaService = $areaService;
        $this->init($request->route('id'));
    }

    /**
    * This Method user for common Rules And Messages
    * Created by Rahul 06/01/2022
    */
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_areas,name|min:3|max:50|',
            'city_id' => 'required|exists:mst_cities,id',
            'state_id' => 'required|exists:mst_states,id',
        ];

        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' => __('api.area.NAME_DUPLICATED'),
            'area_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'area_name_max' => __('api.common.NAME_MAX_LENGTH_ERROR'),
            'city_id_required' =>  __('api.cities.Id_DOES_NOT_EXIST'),
            'city_id_exists' =>  __('api.references.INVALID_CITY_ID'),
            'state_id_required'  => __('api.states.Id_DOES_NOT_EXIST'),
            'state_id_exists' =>  __('api.references.INVALID_STATE_ID'),
        ];
    }

    /**
    * @OA\Get(
    *    path="/admin/area/list",
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
    *   security={{ "apiAuth": {},"PLATFORM" : {}, "CPNYAPIKEY" : {} }}
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
            $haspermission = $this->checkPrivilege($user_data,'#11#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->areaService->list(
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
    *    path="/admin/area/new",
    *    tags={"Admin"},
    *    summary="new area",
    *    operationId="new",
    *
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    
    *    @OA\Parameter(
    *        name="state_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="integer"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="integer"
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
    *   security={{ "apiAuth": {},"PLATFORM" : {}, "CPNYAPIKEY" : {} }}
    *)
    */
    /**
    * Store a new created Action type in storage.
    * Created by Rahul 06/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#12#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $rules = [
                    'name' => $this->rules->name,
                    'city_id' => $this->rules->city_id,
                    'state_id' => $this->rules->state_id,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.min'  => $this->message->area_name_min,
                    'name.unique'  => $this->message->name_exists,
                    'name.max'  => $this->message->area_name_max,
                    'city_id.required' => $this->message->city_id_required,
                    'city_id.exists' => $this->message->city_id_exists,
                    'state_id.required'  => $this->message->state_id_required,
                    'state_id.exists'  => $this->message->state_id_exists,
                ];

                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);

                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {
                    $attributes = $this->attributes($request->all());
                    DB::transaction(function() use ($attributes){
                        $this->areaService->create($attributes);
                        $this->auditLog('Area Created');
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
        $attributes['city_id'] = $request->city_id;
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/area/edit/{id}",
    *    tags={"Admin"},
    *    summary="update area",
    *    operationId="update",
    *
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),    
    *    @OA\Parameter(
    *        name="state_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer"
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
    *   security={{ "apiAuth": {},"PLATFORM" : {}, "CPNYAPIKEY" : {} }}
    *)
    */

    /**
    * Update the specified resource in storage.
    * Created by Rahul 06/01/2022
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#13#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = Area::where("id", $id)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $rules = [
                        'name' => $this->rules->name,
                        'city_id' => $this->rules->city_id,
                        'state_id' => $this->rules->state_id,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.min'  => $this->message->area_name_min,
                        'name.unique'  => $this->message->name_exists,
                        'name.max'  => $this->message->area_name_max,
                        'city_id.required' => $this->message->city_id_required,
                        'city_id.exists' => $this->message->city_id_exists,
                        'state_id.required'  => $this->message->state_id_required,
                        'state_id.exists'  => $this->message->state_id_exists,
                    ];

                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);

                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        DB::transaction(function() use ($attributes,$id){
                            $this->areaService->update($attributes,$id);
                            $this->auditLog('Area Updated');
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
    *    path="/admin/area/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete area",
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
    *   security={{ "apiAuth": {},"PLATFORM" : {}, "CPNYAPIKEY" : {} }}
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
            $haspermission = $this->checkPrivilege($user_data,'#15#');
            
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = Area::where("id", $id)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->areaService->delete($id);
                        $this->auditLog('Area Deleted');
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
    *    path="/admin/area/activate/{id}",
    *    tags={"Admin"},
    *    summary="change area status",
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
            $haspermission = $this->checkPrivilege($user_data,'#14#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = Area::where("id", $id)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->areaService->activate($attributes,$id);
                        $this->auditLog('Area Activated');
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
    *    path="/admin/area/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change area status",
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
    * Deactivate the specified resource from storage.
    * Created by Rahul 06/01/2022
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#14#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = Area::where("id", $id)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->areaService->deactivate($attributes,$id);
                        $this->auditLog('Area Deactivated');
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
    *    path="/admin/area/lov_by_city/{id}",
    *    tags={"Admin"},
    *    summary="get area record",
    *    operationId="lov",
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=false,
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
    * Created by Rahul 11/01/2022  
    * @param  \App\Location  $id 
    * @return \Illuminate\Http\Response
    */
    public function lov(Request $request,$id="")
    {
        try{ 
            $checkArea = DB::table('mst_areas')->where("id", $id)->first();
            if(!empty($id) &&  $checkArea)
            {
                $area = $this->areaService->lov($id,['id','name','location_id','city_id','state_id']);
                $response = $this->setResponse('SUCCESS', [''], ['area'=> $area]);
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
            }            
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

}
