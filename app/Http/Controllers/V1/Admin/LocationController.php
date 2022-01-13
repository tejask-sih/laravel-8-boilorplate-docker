<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use App\Models\Admin\Location;
use App\Services\V1\Admin\LocationService;

class LocationController extends BaseController
{
    protected $locationService;
    protected $rule, $message;

    public function __construct(LocationService $locationService,Request $request)
    {
        //$this->middleware('permission',['except' => $this->getActions(26)]); // means allow 
        $this->locationService = $locationService;
        $this->init($request->route('id'));
    }
    /**
    * This Method user for common Rules And Messages
    */
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_locations,name,'.$id.'|min:3|max:50|',
            'short_name' => 'required|unique:mst_locations,short_name,'.$id.'|min:3',
            'zone' => 'required',
            'primary_number' => 'required|numeric|unique:mst_locations,primary_number,'.$id.'|min:3',
            'alternate_number1' =>'min:10|nullable',
            'alternate_number2' =>'min:10|nullable',
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' => __('api.location.NAME_DUPLICATED'),
            'location_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'location_name_max' => __('api.location.NAME_MAX_LENGTH_ERROR'),
            'short_name_required'  => __('api.location.SHORT_NAME_REQUIRED'),
            'short_name_exists' => __('api.location.SHORT_NAME_DUPLICATED'),
            'short_name_min' => __('api.location.SHORT_NAME_LENGTH_ERROR'),
            'zone_required'  => __('api.location.ZONE_REQUIRED'),
            'primary_number_required'  => __('api.location.PRIMARY_NUMBER_REQUIRED'),
            'primary_number_numeric'  => __('api.location.PRIMARY_NUMBER_LENGTH_ERROR'),
            'primary_number_min'  => __('api.location.PRIMARY_NUMBER_LENGTH_ERROR'),
            'primary_number_exists'  => __('api.location.PRIMARY_NUMBER_DUPLICATED'),
            'alternate_number1_min'  => __('api.location.ALTERNATE_NUMBER_LENGTH_ERROR'),
            'alternate_number2_min'  => __('api.location.ALTERNATE_NUMBER_LENGTH_ERROR'),
        ];
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    /** @OA\Get(
    *    path="/admin/location/list",
    *    tags={"Admin"},   
    *    summary="list",
    *    operationId="location_list",
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
            $haspermission = $this->checkPrivilege($user_data,'#30#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->locationService->list(
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
    *    path="/admin/location/new",
    *    tags={"Admin"},
    *    summary="new",
    *    operationId="location_new",
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
    *        name="zone",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="primary_number",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="alternate_number1",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="alternate_number2",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="dms_costing",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address1",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address2",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address3",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="zipcode",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="state",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_contact",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_phone",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_email",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_contact",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_phone",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_email",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
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
    * Created by Rahul 11/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();           
            $haspermission = $this->checkPrivilege($user_data,'#31#');
            //pr($haspermission);
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $rules = [
                    'name' => $this->rules->name,
                    // 'short_name' => $this->rules->short_name,
                    'zone' => $this->rules->zone,
                    'primary_number' => $this->rules->primary_number,
                    'alternate_number1' => $this->rules->alternate_number1,
                    'alternate_number2' => $this->rules->alternate_number2,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.min'  => $this->message->location_name_min,
                    'name.unique'  => $this->message->name_exists,
                    'name.max'  => $this->message->location_name_max,
                    // 'short_name.required'  => $this->message->short_name_required,
                    // 'short_name.unique'  => $this->message->short_name_exists,
                    // 'short_name.min'  => $this->message->short_name_min,
                    'zone.required'  => $this->message->zone_required,
                    'primary_number.required'  => $this->message->primary_number_required,
                    'primary_number.numeric'  => $this->message->primary_number_numeric,
                    'primary_number.unique'  => $this->message->primary_number_exists,
                    'primary_number.min'  => $this->message->primary_number_min,
                    'alternate_number1.min'  => $this->message->alternate_number1_min,
                    'alternate_number2.min'  => $this->message->alternate_number2_min,
                ];

                $params = $request->all();
                $validator = Validator::make($params, $rules, $customMessages);
                if ($validator->fails()) {
                    $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                } else {
                    $attributes = $this->attributes($request->all());
                    DB::transaction(function() use ($attributes){
                        $this->locationService->create($attributes);
                        $this->auditLog('Location Created');
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
    * Created by Rahul 11/01/2022
    * This Method use for manage requested atatribute for Create and Update 
    */
    private function attributes($request)
    {
        $request = (object) $request;
        $attributes['name'] = $request->name;
        // $attributes['short_name'] = $request->short_name;
        $attributes['zone'] = $request->zone;
        $attributes['primary_number'] = $request->primary_number;
        $attributes['alternate_number1'] = $request->alternate_number1;
        $attributes['alternate_number2'] = $request->alternate_number2;
        $attributes['dms_costing'] = $request->dms_costing;
        $attributes['address1'] = $request->address1;
        $attributes['address2'] = $request->address2;
        $attributes['address3'] = $request->address3;
        $attributes['city'] = $request->city;
        $attributes['zipcode'] = $request->zipcode;
        $attributes['state'] = $request->state;
        $attributes['sales_contact'] = $request->sales_contact;
        $attributes['sales_phone'] = $request->sales_phone;
        $attributes['sales_email'] = $request->sales_email;
        $attributes['service_contact'] = $request->service_contact;
        $attributes['service_phone'] = $request->service_phone;
        $attributes['service_email'] = $request->service_email;
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/location/edit/{id}",
    *    tags={"Admin"},
    *    summary="edit",
    *    operationId="location_edit",
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *            type="integer",
    *            format="int64"  
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
    *        name="zone",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="primary_number",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="alternate_number1",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="alternate_number2",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="dms_costing",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address1",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address2",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="address3",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="zipcode",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="state",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_contact",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_phone",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="sales_email",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_contact",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_phone",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *     @OA\Parameter(
    *        name="service_email",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
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
    * Created by Rahul 11/01/2022
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#32#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkLocation = Location::where("id", $id)->first(); // using from Trait Class method
                if(!$checkLocation){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_LOCATION_ID')]);
                } else {
                
                    $rules = [
                        'name' => $this->rules->name,
                        // 'short_name' => $this->rules->short_name,
                        'zone' => $this->rules->zone,
                        'primary_number' => $this->rules->primary_number,
                        'alternate_number1' => $this->rules->alternate_number1,
                        'alternate_number2' => $this->rules->alternate_number2,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.min'  => $this->message->location_name_min,
                        'name.unique'  => $this->message->name_exists,
                        'name.max'  => $this->message->location_name_max,
                        // 'short_name.required'  => $this->message->short_name_required,
                        // 'short_name.unique'  => $this->message->short_name_exists,
                        // 'short_name.min'  => $this->message->short_name_min,
                        'zone.required'  => $this->message->zone_required,
                        'primary_number.required'  => $this->message->primary_number_required,
                        'primary_number.numeric'  => $this->message->primary_number_numeric,
                        'primary_number.unique'  => $this->message->primary_number_exists,
                        'primary_number.min'  => $this->message->primary_number_min,
                        'alternate_number1.min'  => $this->message->alternate_number1_min,
                        'alternate_number2.min'  => $this->message->alternate_number2_min,
                    ];

                    $params = $request->all();
                    $validator = Validator::make($params, $rules, $customMessages);
                    if ($validator->fails()) {
                        $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
                    } else {
                        $attributes = $this->attributes($request->all());
                        DB::transaction(function() use ($attributes,$id){
                            $this->locationService->update($attributes,$id);
                            $this->auditLog('Location Updated');
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
    *    path="/admin/location/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete location",
    *    operationId="location_destroy",    
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
    * Created by Rahul 11/01/2022
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#34#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkLocation = Location::where("id", $id)->first(); // using from Trait Class method
                if(!$checkLocation){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_LOCATION_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->locationService->delete($id);
                        $this->auditLog('Location Deleted');
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
    *    path="/admin/location/activate/{id}",
    *    tags={"Admin"},
    *    summary="change location status",
    *    operationId="location_activate",    
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
    * Created by Rahul 11/01/2022
    * @param  \App\Location  $id
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#33#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkLocation = Location::where("id", $id)->first(); // using from Trait Class method
                if(!$checkLocation){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_LOCATION_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->locationService->activate($attributes,$id);
                        $this->auditLog('Location Activated');
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
    *    path="/admin/location/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change location status",
    *    operationId="location_deactivate",    
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
    * Created by Rahul 11/01/2022
    * @param  \App\Location  $id
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#4#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkLocation = Location::where("id", $id)->first(); // using from Trait Class method
                if(!$checkLocation){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_LOCATION_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->locationService->deactivate($attributes,$id);
                        $this->auditLog('Location Deactivated');
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
    *    path="/admin/location/lov/{id}",
    *    tags={"Admin"},
    *    summary="get location record",
    *    operationId="location_lov",
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
    * @param  \App\Location  $id  Optional
    * @return \Illuminate\Http\Response
    */
    public function lov(Request $request,$id="")
    {
        try{ 
            $checkLocation = DB::table('mst_locations')->where("id", $id)->first();
            if(!empty($id) &&  $checkLocation)
            {
                $location = $this->locationService->getLocationList($id,[
                    'id','name','zone','primary_number','alternate_number1','alternate_number2','dms_costing','address1','address2','address3','city','zipcode','state','sales_contact','sales_phone','sales_email','service_contact','service_phone','service_email'
                ]);
                $response = $this->setResponse('SUCCESS', [''], ['location'=> $location]);
            }elseif(!$checkLocation)
            {
                $location = $this->locationService->getLocationList(['id','name','zone','primary_number','alternate_number1','alternate_number2','dms_costing','address1','address2','address3','city','zipcode','state','sales_contact','sales_phone','sales_email','service_contact','service_phone','service_email']);
                $response = $this->setResponse('SUCCESS', [''], ['location'=> $location]); 
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_STATE_ID')]);
            }            
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

}
