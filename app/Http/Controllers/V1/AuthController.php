<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\BaseController;
use DB;
use Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use Carbon\Carbon;
use App\Traits\CommonTrait;

class AuthController extends BaseController
{
    use CommonTrait;
    public function __construct()
    {
       
    }   
    /**  
    * Get the guard to be used authentication.
    * Created by Rahul 03/01/2022   
    */
    public function guard()
    {
        return Auth::guard();
    }

    /**  
    * Get a JWT token via given credentials 
    * Created by Rahul 03/01/2022         
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */
    public function init(Request $request){
        try{            
            $companyData = Company::select('id','api_key','hero_image_id','logo_id','favicon_id','theme_color')->first();
            if(empty($companyData)) {
                $response = $this->setResponse('OTHER_ERROR', [__('api.notifications.INVALID_COMPANY')]);           
            } else {
                $expire_date = '2030-03-11 18:53:42';
                if (Carbon::parse($expire_date) < Carbon::now()) {
                    $response = $this->setResponse('LICENSE_EXPIRED');
                } else {
                    $company = (object) [];
                    $branding = (object) [];
                    $branding->id = $companyData->id;                    
                    $branding->api_key = $companyData->api_key;
                    $branding->theme_color = $companyData->theme_color;                    
                    $company->branding = $branding;
                    $response = $this->setResponse('SUCCESS', [__('api.notifications.INIT_SUCCESSFULL')], ['company' => $company]);
                }
            }            
            return send_response($request,$response);
        } catch (\Exception $e) {
            $response = $this->setResponse('OTHER_ERROR', [$e->getMessage()]);
            return send_response($request,$response);            
        }
    } 

    /**
    * @OA\Post(
    *    path="/auth/login",
    *    tags={"Authentication"},
    *    summary="Login",
    *    operationId="login",
    *
    *    @OA\Parameter(
    *        name="email",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",
    *            example="admin@gmail.com"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="password",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",
    *            example="123456"
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
    * Get a JWT token 
    * Created by Rahul 03/01/2022         
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */  
       
    public function login(Request $request)
    { 
        try{
            $rules = array(
                'email' => [
                    'required', 
                    'email', 
                    'exists:users'
                ],
                'password' => [
                    'required', 
                    'string', 
                    config('CommonValidator.min.password'), // must be at least 08 characters in length 
                    config('CommonValidator.regex.password'), // must contain a valid password                  
                ],
            );
            $params = $request->all();
            $validator = Validator::make($params, $rules);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()], 422);
            }else{
                $postData = $request->all();            
                // $token_expiry_time = 720 * 60;  // 60 * 60 = 3600 second means 1 minutes, 12 hr = 43,200 Seconds
                $companyData = Company::select('session_timeout')->first();


                $tk_expiry = $companyData->session_timeout * 60; 
                $request['status'] = 1;
                $credentials = $request->only('email', 'password','status');
                $expiryIn = $tk_expiry; 
                $this->guard()->factory()->setTTL($expiryIn);            
                $token  =  $this->guard()->attempt($credentials); 
                
                if ($token = $this->guard()->attempt($credentials)) 
                {
                    $this->auditLog('Logged In');
                    return response()->json(['success' => true,'token' => $token,'NOTIFICATION'=>'LOGIN_SUCCESSFUL']);
                }else{
                    $errors['error'] = ['Email or Password is invalid'];                   
                    return response()->json(['LOGIN_FAILED'=>$errors], 423);
                } 
            }
        }catch (\Exception $e) {
            $response = $this->setResponse('OTHER_ERROR',[$e->getMessage()]);           
            return send_response($request,$response);            
        }
    }

    /*  New user registration 
    *   Created by Rahul 03/01/2022
    */
    /**
    * @OA\Post(
    *    path="/auth/register",
    *    tags={"Authentication"},
    *    summary="Register",
    *    operationId="Register",
    *
    *    @OA\Parameter(
    *        name="email",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string",            
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="name",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    * 
    *    @OA\Parameter(
    *        name="password",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="role_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="department_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="designation_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="level_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="location_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="premises_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="pay_type_id",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="username",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="phone",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="tcode",
    *        in="query",
    *        required=false,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="privileges",
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
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */ 

    /**  
    * Register new user
    * Created by Rahul 03/01/2022         
    * @param  \Illuminate\Http\Request $request     
    * @return \Illuminate\Http\JsonResponse
    */ 


    public function register(Request $request)
    {
        $rules = array(
            'email' => [
                'required', 
                'email', 
                'unique:users,email'
            ],
            'password' => [
                'required', 
                'string',
                config('CommonValidator.min.password'), // must be at least 08 characters in length 
                config('CommonValidator.regex.password'), // must contain a valid password
            ],

        );
        $params = $request->all();
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        } else {
            $user = New User();
            $user->email = $request->email;
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->department_id = $request->department_id;
            $user->designation_id = $request->designation_id;
            $user->level_id = $request->level_id;
            $user->location_id = $request->location_id;
            $user->premises_id = $request->premises_id;
            $user->pay_type_id = $request->pay_type_id;
            $user->reporting_to = $request->reporting_to;
            $user->avatar_id = $request->avatar_id;
            $user->username = $request->username;
            $user->primary_number = $request->phone;
            $user->salary = $request->salary;
            $user->date_join = $request->date_join;
            $user->tcode = $request->tcode;
            $user->address = $request->address;
            $user->privileges = $request->privileges;
            $user->ip_address = $request->ip_address;
            $user->password = bcrypt($request->password);
            $user->status = '1';
            $user->save(); 
            return response()->json(['success' => true,'data' => $user,'message' => 'User Save Successfully']);
        }
    }

    /**
    * @OA\Post(
    *    path="/auth/forgot-password",
    *    tags={"Authentication"},
    *    summary="Forgot Password",
    *    operationId="forgot_password",
    *
    *    @OA\Parameter(
    *        name="email",
    *        in="query",
    *        required=true,
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
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }}
    *)
    */

    /**
    * Forgot Password functionality we will validate email then send forgot password link
    * to email id.
    * created by Rahul 05/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    public function forgotPassword(Request $request)
    {
        try {
            $rules = array(
                'email' => [
                    'required', 
                    'email',
                ],
            );

            $customMessages = [
                'email.required' => __('api.common.EMAIL_REQUIRED'),
                'email.email' => __('api.common.EMAIL_INVALID'),
            ];

            $params = $request->all();
            $validator = Validator::make($params, $rules, $customMessages);
            if ($validator->fails()) {
                $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
            } else {
                $user = User::where('email',$request->email)->first();

                if(empty($user)){
                    $errors['EMAIL'] = [__('api.common.EMAIL_INVALID')];
                    $response = $this->setResponse('VALIDATION_ERROR',$errors);
                }else{

                    $six_digit_random_number = mt_rand(100000, 999999);
                    $emailTemplate = EmailTemplate::where('id',1)->where('status',1)->select("subject","body")->first();

                    if($emailTemplate!='') {
                        $subject = $emailTemplate->subject;
                        $find_arr = array("##NAME##" , "##OTP##");
                        $replce_arr = array($user->name, $six_digit_random_number);
                        $body = str_replace($find_arr, $replce_arr, $emailTemplate->body);
                        //pr($user->email);
                        $this->sendEmail($user->email,$subject,$body);
                        //$this->sendEmail('rahulpatelsiliconithub@gmail.com',$subject,$body);
                        $response = $this->setResponse('SUCCESS',[__('api.common.EMAIL_SENT')]);
                    } else {
                        $response = $this->setResponse('SUCCESS',[__('api.common.EMAIL_IS_INACTIVE')]);
                    }
                    //DB::enableQueryLog();
                    /* update tempkey string in users table  */
                    $updateData = ['otp'=>$six_digit_random_number];
                    $updatekey = User::where('email',$request->email)->update($updateData);

                    //$query = DB::getQueryLog();   
                    if ($updatekey > 0) {
                        $response = $response;
                    }else{
                        $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.OTHER_ERROR')]);
                    }
                } 
            }
            return send_response($request,$response);
        }catch (\Exception $e) {
            $response = $this->setResponse('OTHER_ERROR', [$e->getMessage()]);
            return send_response($request,$response);            
        }
    }

    /**
    * @OA\Post(
    *    path="/auth/reset-password",
    *    tags={"Authentication"},
    *    summary="Reset Password",
    *    operationId="reset_password",
    *
    *    @OA\Parameter(
    *        name="email",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="otp",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="new_password",
    *        in="query",
    *        required=true,
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
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }}
    *)
    */
    /**
    * reset Password functionality we will validate email then send forgot password opt.
    * created by Rahul 05/01/2022
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    public function resetPassword(Request $request)
    {
        try {
            $rules = array(
                'email' => [
                    'required',
                    'email'
                ],
                'otp' => [
                    'required',
                    config('CommonValidator.digits.otp')
                ],
                'new_password' => [
                    'required',
                    'string',
                    config('CommonValidator.min.password'), // must be at least 08 characters in length 
                    config('CommonValidator.regex.password'), // must contain a valid password
                ],
            );

            $customMessages = [
                'email.required' => __('api.common.EMAIL_REQUIRED'),
                'email.email' => __('api.common.EMAIL_INVALID'),
                'otp.required' => __('api.common.OTP_REQUIRED'),
                'otp.digits' => __('api.common.6_DIGITS_OTP'),
                'new_password.required' => __('api.common.NEW_PASSWORD_REQUIRED'),
                'new_password.min' => __('api.common.NEW_PASSWORD_MIN_LENGTH_ERROR'),
                'new_password.regex' => __('api.common.NEW_PASSWORD_MUST_VALID_PASSWORD'),                
            ];

            $params = $request->all();
            $validator = Validator::make($params, $rules, $customMessages);
            if ($validator->fails()) {
                $response = $this->setResponse('VALIDATION_ERROR',$validator->errors());
            } else {
                $user = User::where('email',$request->email)->first();

                if(empty($user)){
                    $errors['EMAIL'] = [__('api.common.EMAIL_INVALID')];
                    $response = $this->setResponse('VALIDATION_ERROR',$errors);
                }else{
                    /* verify otp */
                    $otp = $request->otp;
                    $password = $request->new_password;                    
                    
                    if(!$user->otp || $user->otp == NULL){
                        $errors['OTP'] = [__('api.common.OTP_NOT_AVAILABLE')];
                        $response = $this->setResponse('VALIDATION_ERROR',$errors);
                        return send_response($request,$response);
                    }                   

                    if($user->otp != $otp){
                        $errors['OTP'] = [__('api.common.OTP_INVALID')];
                        $response = $this->setResponse('VALIDATION_ERROR',$errors);
                        return send_response($request,$response);
                    }                    
                    $emailTemplate = EmailTemplate::where('id',2)->where('status',1)->select("subject","body")->first();

                    if($emailTemplate!='') {
                        $subject = $emailTemplate->subject;

                        $find_arr = array("##NAME##" , "##PASSWORD##");
                        $replce_arr = array($user->name, $password);
                        $body = str_replace($find_arr, $replce_arr, $emailTemplate->body);
                        $this->sendEmail('rahulpatelsiliconithub@gmail.com',$subject,$body);
                        $this->sendEmail($user->email,$subject,$body);
                        $response = $this->setResponse('SUCCESS',[__('api.common.EMAIL_SENT')]);
                    } else {
                        $response = $this->setResponse('SUCCESS',[__('api.common.EMAIL_IS_INACTIVE')]);
                    }  
                    /* update password in users table  */
                    $updateData = ['otp' => NULL, 'password' => bcrypt($password)];
                    $updatekey = $user->update($updateData);
                    if ($updatekey > 0) {
                        $response = $response;                    
                    } else {
                        $response = $this->setResponse('OTHER_ERROR',[__('api.notifications.OTHER_ERROR')]);
                    }
                }
            }
            return send_response($request,$response);
        } catch (\Exception $e) {
            $response = $this->setResponse('OTHER_ERROR', [$e->getMessage()]);
            return send_response($request,$response);            
        }
    } 

    /**
    * @OA\Get(
    *    path="/auth/logout",
    *    tags={"Authentication"},
    *    summary="Logout",
    *    operationId="logout",   
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
    *        response=404,
    *        description="not found"
    *    ),
    *   security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }}
    *)
    */  

    /**  
    * Logged out (token expire)
    * Created by Rahul 03/01/2022 
    * @return \Illuminate\Http\JsonResponse
    */ 
    public function logout(Request $request)
    {
        $this->guard()->logout();
        //$this->auditLog('Logged Out');
        return response()->json(['message' => 'Successfully logged out','NOTIFICATION'=>'LOGOUT']);
    }    

    /**  
    * Refresh a token
    * Created by Rahul 03/01/2022 
    * @return \Illuminate\Http\JsonResponse
    */ 
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh()); 
    }    

    /**     
    *   @OA\Get(
    *   path="/auth/me",
    *   tags={"Authentication"},
    *   summary="me",
    *       operationId="me",         
    *   @OA\Response(
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
    *    security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }}           
                 
        )
    */
    /**  
    * Get authenticate user
    * Created by Rahul 03/01/2022 
    * @return \Illuminate\Http\JsonResponse
    */ 
       
    public function me(Request $request)
    {
        try{
            $user = $this->guard()->user();
            $response = $this->setResponse('SUCCESS', [''], ['user'=> $user]);
        }catch(\Exception $e)
        {
            $response = $this->setResponse('OTHER_ERROR', [$e->getMessage()]);
        } 
        return send_response($request,$response); 
    }
    
    /**
    * @OA\Post(
    *    path="/auth/change-password",
    *    tags={"Authentication"},
    *    summary="Change-Password",
    *    operationId="Change-Password",
    *    
    *    @OA\Parameter(
    *        name="old_password",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),
    *    @OA\Parameter(
    *        name="new_password",
    *        in="query",
    *        required=true,
    *        @OA\Schema(
    *            type="string"
    *        )
    *    ),  
    *    @OA\Parameter(
    *        name="confirm_password",
    *        in="query",
    *        required=true,
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
    *   security={{ "apiAuth": {},"PLATFORM" : {},"CPNYAPIKEY" : {} }}    
    *)
    */ 

    /**  
    * Authenticate user change password
    * created by Rahul 04/01/2022 
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */ 
    public function changepassword(Request $request){

        $rules = array(
            'old_password' => [
                'required',
                'string',
                config('CommonValidator.min.password'), // must be at least 08 characters in length 
                config('CommonValidator.regex.password'), // must contain a valid password
            ],
            'new_password' => [
                'required',
                'string',
                'required_with:confirm_password',
                'same:confirm_password',
                config('CommonValidator.min.password'), // must be at least 08 characters in length 
                config('CommonValidator.regex.password'), // must contain a valid password
            ],
            'confirm_password' => [
                'required',
                'string',
                config('CommonValidator.min.password'), // must be at least 08 characters in length 
                config('CommonValidator.regex.password'), // must contain a valid password
            ],
        );

        $params = $request->all();
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        } else {
            $request['email'] = Auth::user()->email;
            $request['password'] = $request->old_password;
            $credentials = $request->only('email','password');
            if($token = $this->guard()->attempt($credentials)){
                $data = ['password'=>bcrypt($request->new_password)];
                $res = User::where('email',Auth::user()->email)->update($data);
                if($res > 0){
                    /* After change password login in again */
                    $request['password'] = $request->new_password;               
                    $credentials = $request->only('email', 'password');
                    /* After successfully update password we send response with new token */
                    if ($token = $this->guard()->attempt($credentials)) {
                        return response()->json(['status' => 200,'message'=>'Password Changed Successfull','token'=>$this->respondWithToken($token)]);
                    }else{
                        $errors['email'] = ['Something went wrong'];
                        return response()->json(['errors'=>$errors], 422);
                    }
                }else{
                    $errors['password'] = ['Please enter valid old password'];
                    return response()->json(['errors'=>$errors], 422);
                }
            }else{
                $errors['password'] = ['Please enter valid old password'];
                return response()->json(['errors'=>$errors], 422);
            }
        }
    }
    /**     
    *   @OA\Get(
    *   path="/auth/list-user",
    *   tags={"Authentication"},
    *   summary="me",
    *       operationId="listuser",         
    *   @OA\Response(
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
    *    security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }} 
        )
    */

    /**  
    * User list
    * created by Rahul 04/01/2022     
    * @return \Illuminate\Http\JsonResponse
    */ 

    public function listuser(){
        $res = User::get(); 
        $count = User::get()->count();
        return response()->json(['status' => 200,'total'=>$count,'data'=>$res]);
    }

    /**     
    *   @OA\Get(
    *   path="/auth/user-delete/{id}",
    *   tags={"Authentication"},
    *   summary="user-delete",
    *   operationId="user-delete", 
    *   @OA\Parameter(   
    *       name="id",   
    *       in="path",   
    *       required=true,
    *       @OA\Schema(
    *           type="integer",       
    *           format="int64"
    *       )
    *   ), 
    *   @OA\Response(
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
    *    security={{ "apiAuth": {}, "PLATFORM" : {}, "CPNYAPIKEY" : {} }}           
                 
        )
    */

    /**  
    * Remove specific user resource
    * created by Rahul 04/01/2022     
    * @return \Illuminate\Http\JsonResponse
    */ 
    public function deleteuser($id){       
        $user = User::where('id',$id)->delete();
        if($user > 0)
        {
            return response()->json(['status' => 200,'data'=>$user]);
        }else{
            $errors['user_delete'] = [__('api.notifications.OTHER_ERROR')];
            return response()->json(['status'=>$errors], 422);
        }       
    }


    
}
