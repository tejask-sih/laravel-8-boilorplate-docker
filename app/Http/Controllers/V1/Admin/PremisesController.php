<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use App\Models\Admin\Premises; 
use App\Services\V1\Admin\PremisesService;

class PremisesController extends BaseController
{
    protected $premisesService;
    protected $rule, $message;

    public function __construct(PremisesService $premisesService,Request $request)
    {
        $this->premisesService = $premisesService;
        $this->init($request->route('id'));
    }

    /**
    * Created by Rahul 11/01/2022 
    * This Method user for common Rules And Messages
    */   
    public function init($id = null) 
    {
        $this->rules = (object) [
            'name' => 'required|unique:mst_premises,name,'.$id.'|min:3|max:50|',
            'short_name' => 'required|unique:mst_premises,short_name,'.$id.'|min:2|max:50|',
            'location_id' => 'required|exists:mst_locations,id',
            'type_id' => 'required|exists:mst_premises_types,id',
            'city_id' => 'required|exists:mst_cities,id',
            'state_id' => 'required|exists:mst_states,id',
            'addess_line1' => 'required',
            'addess_line2' => 'required',
            'zipcode' => 'required',            
            'primary_number' => 'required|numeric|unique:mst_premises,primary_number,'.$id.'|min:3',
            'alternate_number1' =>'min:10|nullable',
            'alternate_number2' =>'min:10|nullable',
        ];
        $this->message = (object) [
            'name_required'  => __('api.common.NAME_REQUIRED'),
            'name_exists' =>  __('api.premise.NAME_DUPLICATED'),
            'short_name_required'  => __('api.common.SHORT_NAME_REQUIRED'),
            'short_name_exists' =>  __('api.premise.SHORT_NAME_DUPLICATED'),
            'short_name_min' => __('api.premise.SHORT_NAME_LENGTH_ERROR'),
            'premise_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'premise_name_max' => __('api.common.NAME_MAX_LENGTH_ERROR'),  
            'premise_short_name_min' => __('api.common.NAME_MIN_LENGTH_ERROR'),
            'premise_short_name_max' => __('api.common.NAME_MAX_LENGTH_ERROR'),
            'location_id_required' =>  __('api.locations.Id_DOES_NOT_EXIST'),
            'location_id_exists' =>  __('api.references.INVALID_LOCATION_ID'),
            'type_id_required' =>  __('api.premises_types.Id_DOES_NOT_EXIST'),
            'type_id_exists' =>  __('api.references.INVALID_PREMISES_TYPE_ID'),
            'city_id_required' =>  __('api.cities.Id_DOES_NOT_EXIST'),
            'city_id_exists' =>  __('api.references.INVALID_CITY_ID'),
            'state_id_required'  => __('api.states.Id_DOES_NOT_EXIST'),
            'state_id_exists' =>  __('api.references.INVALID_STATE_ID'),
            'addess_line1_required'  => __('api.premise.ADDRESS1_REQUIRED'),
            'addess_line2_required'  => __('api.premise.ADDRESS2_REQUIRED'),
            'zipcode_required'  => __('api.premise.ZIPCODE_REQUIRED'),
            'primary_number_required'  => __('api.premise.PRIMARY_NUMBER_REQUIRED'),
            'primary_number_numeric'  => __('api.premise.PRIMARY_NUMBER_LENGTH_ERROR'),
            'primary_number_exists' =>  __('api.premise.PRIMARY_NUMBER_DUPLICATED'),
            'primary_number_min' => __('api.premise.PRIMARY_NUMBER_LENGTH_ERROR'), 
            'alternate_number1_min'  => __('api.premise.ALTERNATE_NUMBER_LENGTH_ERROR'),
            'alternate_number2_min'  => __('api.premise.ALTERNATE_NUMBER_LENGTH_ERROR'),
        ];
    }

    /** @OA\Get(
    *    path="/admin/premises/list",
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
            $haspermission = $this->checkPrivilege($user_data,'#40#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $page = (int)$request->page;
                $perPage = (int)$request->per_page;
                $paginator = $this->premisesService->list(
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
    *    path="/admin/premises/new",
    *    tags={"Admin"},
    *    summary=" new",
    *    operationId="new",
    *    @OA\Parameter(
    *        name="location_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer",
    *            format="int64"           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="type_id",
    *        in="query",
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
    *            type="string",           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="short_name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="addess_line1",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="addess_line2",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer",
    *            format="int64"            
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
    *        name="zipcode",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="string",                    
    *        )
    *    ),  
    *    @OA\Parameter(
    *        name="primary_number",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="alternate_number1",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="alternate_number2",
    *        in="query",
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
    * Store a new created Action type in storage.
    * Created by Rahul 11/01/2022        
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */
    public function new(Request $request)
    {
        try {
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#41#');
            if(!empty($haspermission) && $haspermission == 'Yes'){                
                $rules = [
                    'name' => $this->rules->name,
                    'short_name' => $this->rules->short_name,
                    'location_id' => $this->rules->location_id,
                    'type_id' => $this->rules->type_id,
                    'city_id' => $this->rules->city_id,
                    'state_id' => $this->rules->state_id,
                    'addess_line1' => $this->rules->addess_line1,
                    'addess_line2' => $this->rules->addess_line2,
                    'zipcode' => $this->rules->zipcode,
                    'primary_number' => $this->rules->primary_number,
                    'alternate_number1' => $this->rules->alternate_number1,
                    'alternate_number2' => $this->rules->alternate_number2,
                ];
                $customMessages = [
                    'name.required'  => $this->message->name_required,
                    'name.unique'  => $this->message->name_exists,
                    'name.min'  => $this->message->premise_name_min,
                    'name.max'  => $this->message->premise_name_max,
                    'short_name.required'  => $this->message->short_name_required,
                    'short_name.unique'  => $this->message->short_name_exists,
                    'short_name.min'  => $this->message->premise_short_name_min,
                    'short_name.max'  => $this->message->premise_short_name_max,
                    'location_id.required' => $this->message->location_id_required,
                    'location_id.exists' => $this->message->location_id_exists,
                    'type_id.required' => $this->message->type_id_required,
                    'type_id.exists' => $this->message->type_id_exists,
                    'city_id.required' => $this->message->city_id_required,
                    'city_id.exists' => $this->message->city_id_exists,
                    'state_id.required'  => $this->message->state_id_required,
                    'state_id.exists'  => $this->message->state_id_exists,
                    'addess_line1.required' => $this->message->addess_line1_required,
                    'addess_line2.required' => $this->message->addess_line2_required,
                    'zipcode.required' => $this->message->zipcode_required,                    
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
                    // pr($request->all());
                    $attributes = $this->attributes($request->all());
                    DB::transaction(function() use ($attributes){
                        $this->premisesService->create($attributes);
                        $this->auditLog('Premises Created');
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
        $attributes['location_id'] = $request->location_id;
        $attributes['type_id'] = $request->type_id;
        $attributes['name'] = $request->name;
        $attributes['short_name'] = $request->short_name;
        $attributes['addess_line1'] = $request->addess_line1;
        $attributes['addess_line2'] = $request->addess_line2;
        $attributes['state_id'] = $request->state_id;
        $attributes['city_id'] = $request->city_id;
        $attributes['zipcode'] = $request->zipcode;
        $attributes['primary_number'] = $request->primary_number;
        $attributes['alternate_number1'] = @$request->alternate_number1;
        $attributes['alternate_number2'] = @$request->alternate_number2; 
        return $attributes;
    }

    /**
    * @OA\Post(
    *    path="/admin/premises/edit/{id}",
    *    tags={"Admin"},
    *    summary="update premises",
    *    operationId="update",
    *    @OA\Parameter(
    *        name="id",
    *        in="path",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"              
    *        )
    *    ),  
    *    @OA\Parameter(
    *        name="location_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer",
    *            format="int64"           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="type_id",
    *        in="query",
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
    *            type="string",           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="short_name",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="addess_line1",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="addess_line2",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",           
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="city_id",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="integer",
    *            format="int64"            
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
    *        name="zipcode",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="string",                    
    *        )
    *    ),  
    *    @OA\Parameter(
    *        name="primary_number",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="alternate_number1",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"         
    *        )
    *    ), 
    *    @OA\Parameter(
    *        name="alternate_number2",
    *        in="query",
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
    * Update the specified resource in storage
    * Created by Rahul 11/01/2022         
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#42#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremises = Premises::where("id", $id)->first(); // using from Trait Class method
                if(!$checkPremises){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_ID')]);
                } else {

                    $rules = [
                        'name' => $this->rules->name,
                        'short_name' => $this->rules->short_name,
                        'location_id' => $this->rules->location_id,
                        'type_id' => $this->rules->type_id,
                        'city_id' => $this->rules->city_id,
                        'state_id' => $this->rules->state_id,
                        'addess_line1' => $this->rules->addess_line1,
                        'addess_line2' => $this->rules->addess_line2,
                        'zipcode' => $this->rules->zipcode,
                        'primary_number' => $this->rules->primary_number,
                        'alternate_number1' => $this->rules->alternate_number1,
                        'alternate_number2' => $this->rules->alternate_number2,
                    ];
                    $customMessages = [
                        'name.required'  => $this->message->name_required,
                        'name.unique'  => $this->message->name_exists,
                        'name.min'  => $this->message->premise_name_min,
                        'name.max'  => $this->message->premise_name_max,
                        'short_name.required'  => $this->message->short_name_required,
                        'short_name.unique'  => $this->message->short_name_exists,
                        'short_name.min'  => $this->message->premise_short_name_min,
                        'short_name.max'  => $this->message->premise_short_name_max,
                        'location_id.required' => $this->message->location_id_required,
                       
                        'type_id.required' => $this->message->type_id_required,
                        
                        'city_id.required' => $this->message->city_id_required,
                       
                        'state_id.required'  => $this->message->state_id_required,
                        
                        'addess_line1.required' => $this->message->addess_line1_required,
                        'addess_line2.required' => $this->message->addess_line2_required,
                        'zipcode.required' => $this->message->zipcode_required,                    
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
                            $this->premisesService->update($attributes,$id);
                            $this->auditLog('Premises Updated');
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
    *    path="/admin/premises/destroy/{id}",
    *    tags={"Admin"},
    *    summary="delete premises",
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
    * Created by Rahul 11/01/2022
    * @param  \App\ActionType  $actionType
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#44#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremises = Premises::where("id", $id)->first(); // using from Trait Class method
                if(!$checkPremises){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_ID')]);
                } else {
                    DB::transaction(function() use ($id){
                        $this->premisesService->delete($id);
                        $this->auditLog('Premises Deleted');
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
    *    path="/admin/premises/activate/{id}",
    *    tags={"Admin"},
    *    summary="change premises status",
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
    * Created by Rahul 11/01/2022
    * @param  \App\Premises  $id
    * @return \Illuminate\Http\Response
    */
    public function activate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#43#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremises = Premises::where("id", $id)->first(); // using from Trait Class method               
                if(!$checkPremises){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->premisesService->activate($attributes,$id);
                        $this->auditLog('Premise Activated');
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
    *    path="/admin/premises/deactivate/{id}",
    *    tags={"Admin"},
    *    summary="change premises status",
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
    * Created by Rahul 11/01/2022
    * @param  \App\Premises  $id
    * @return \Illuminate\Http\Response
    */
    public function deactivate(Request $request,$id)
    {
        try{
            $user_data = auth()->user();
            $haspermission = $this->checkPrivilege($user_data,'#43#');
            if(!empty($haspermission) && $haspermission == 'Yes'){
                $checkPremises = Premises::where("id", $id)->first(); // using from Trait Class method               
                if(!$checkPremises){
                    $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_ID')]);
                } else {
                    $attributes = array();
                    DB::transaction(function() use ($attributes,$id){
                        $this->premisesService->deactivate($attributes,$id);
                        $this->auditLog('Premise Deactivated');
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
    *    path="/admin/premises/lov/{id}",
    *    tags={"Admin"},
    *    summary="get premises record",
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
            $checkPremises = DB::table('mst_premises')->where("id", $id)->first();
            if(!empty($id) &&  $checkPremises)
            {
                $premises = $this->premisesService->lov($id,['id','name','type_id','location_id','city_id','state_id']);
                $response = $this->setResponse('SUCCESS', [''], ['premises'=> $premises]);
            }else{
                $response = $this->setResponse('OTHER_ERROR',[__('api.references.INVALID_PREMISES_ID')]);
            }            
        } catch (Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);
        }
        return send_response($request,$response); 
    }

   
}
