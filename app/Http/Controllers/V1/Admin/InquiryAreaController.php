<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use DB;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\Admin\InquiryArea;
use App\Services\V1\Admin\InquieryAreaService;
use Exception;

class InquiryAreaController extends BaseController
{
    protected $inquieryAreaService;
    protected $rule, $message;

    public function __construct(InquieryAreaService $inquieryAreaService,Request $request)
    {        
        $this->inquieryAreaService = $inquieryAreaService;
        $this->init($request->route('id'));
    }
    /**
    * This Method user for common Rules And Messages
    */
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_areas,name|min:3|max:50|',
            'location_id' => 'required|exists:mst_locations,id',
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' => __('api.area.NAME_DUPLICATED'),
            'area_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'area_name_max' => __('api.common.NAME_MAX_LENGTH_ERROR'),
            'location_id_required' =>  __('api.location.Id_DOES_NOT_EXIST'),
            'location_id_exists' =>  __('api.references.INVALID_LOCATION_ID'),
        ];
    }
    
    /** @OA\Get(
    *    path="/admin/inquiry_area/list",
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
    * Created by Rahul 11/01/2022
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#16#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->inquieryAreaService->list(
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
    *    path="/admin/inquiry_area/new",
    *    tags={"Admin"},
    *    summary="new",
    *    operationId="new",
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
    *        name="location_id",
    *        in="query",
    *        required=true,
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
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}
    *)
    */
    /**
    * Store a new created Action type in storage.
    * Created by Rahul 11/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#17#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $rules = [
                    'name' => $this->rules->name,
                    'location_id' => $this->rules->location_id,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.min'  => $this->message->area_name_min,
                    'name.unique'  => $this->message->name_exists,
                    'name.max'  => $this->message->area_name_max,
                    'location_id.required' => $this->message->location_id_required,
                    'location_id.exists' => $this->message->location_id_exists,
                ];

                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);

                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {
                    $attributes = $this->attributes($request->all());
                        $this->inquieryAreaService->create($attributes);
                        $this->auditLog('Inquiry Area Created');
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
    * Created by Rahul 11/01/2022
    * This Method use for manage requested atatribute for Create and Update 
    */
    private function attributes($request)
    {
        $request = (object) $request;
        $attributes['name'] = $request->name;
        $attributes['location_id'] = $request->location_id;
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/inquiry_area/edit/{id}",
    *    tags={"Admin"},
    *    summary="update",
    *    operationId="update",
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="location_id",
    *        in="query",
    *        required=true,
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
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}
    *)
    */
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#18#');
            if(!empty($haspermission) && $haspermission == 'Yes'){

                $checkArea = InquiryArea::where("id", $id)->where('location_id','!=', NULL)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $rules = [
                        'name' => $this->rules->name,
                        'location_id' => $this->rules->location_id,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.min'  => $this->message->area_name_min,
                        'name.unique'  => $this->message->name_exists,
                        'name.max'  => $this->message->area_name_max,
                        'location_id.required' => $this->message->location_id_required,
                        'location_id.exists' => $this->message->location_id_exists,
                    ];

                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);

                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        $this->inquieryAreaService->update($attributes,$id);
                        $this->auditLog('Inquiry Area Updated');
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
    *    path="/admin/inquiry_area/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete",
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
    *
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#20#');
            
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = InquiryArea::where("id", $id)->where('location_id','!=', NULL)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->inquieryAreaService->delete($id);
                        $this->auditLog('Inquiry Area Deleted');
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
    *    path="/admin/inquiry_area/activate/{id}",
    *    tags={"Admin"},
    *    summary="change status",
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
    *
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#19#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = InquiryArea::where("id", $id)->where('location_id','!=', NULL)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->inquieryAreaService->activate($attributes,$id);
                        $this->auditLog('Inquiry Area Activated');
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
    *    path="/admin/inquiry_area/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change status",
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
    *
    * @param  \App\State  $state
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#20#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkArea = InquiryArea::where("id", $id)->where('location_id','!=', NULL)->first(); // using from Trait Class method
                if(!$checkArea){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_AREA_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->inquieryAreaService->deactivate($attributes,$id);
                        $this->auditLog('Inquiry Area Deactivated');
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
    *    path="/admin/inquiry_area/lov_by_location/{location_id}",
    *    tags={"Admin"},
    *    summary="get inquiry area record",
    *    operationId="lov_by_location",
    *    @OA\Parameter(
    *        name="location_id",
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
    * Created by Rahul 07/01/2022   
    * @return \Illuminate\Http\Response
    */
    public function lov_by_location(Request $request,$location_id)
    {
        try{
            $checkinquieryArea = DB::table('mst_areas')->where("location_id", $location_id)->first();
            if(!empty($location_id) &&  $checkinquieryArea)
            {
                $inquieryArea = $this->inquieryAreaService->lov($location_id,['id','name']);            
                $response = $this->setResponse('SUCCESS', [''], ['Inquiery area'=> $inquieryArea]);
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_LOCATION_ID')]);
            }            
        }catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

}
