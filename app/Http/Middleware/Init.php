<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Company;
use Carbon\Carbon;

class Init
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $path = $request->path();
            if(str_contains($path, '/auth/init')){
                return $next($request);
            } else {
                // $result = ['status' => 422,'errors' => ["general" => ['Unauthorized Action']]];
                // return response()->json($result,422);
                $api_key        = self::VerifyAPIKEY($request);
                $platform       = self::verifyPlatform($request);
                $user_account   = self::VeriFyUserAccount($request);         

                if(!empty($api_key)){ 
                    return send_response($request,$api_key);
                } elseif(!empty($platform)) { 
                    return send_response($request,$platform);
                } elseif(!empty($user_account)) {
                    return send_response($request,$platform);
                } else {
                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            $response = BaseController::setResponse('EXCEPTION');
            return response()->json($response,$response['code']);
            // return get_response($request, $data);
        }
    }

    public static function verifyPlatform(Request $request)
    {
        $data = array();
        $platform = $request->header('PLATFORM');         
        $val_platform =  explode(',', config('CommonValidator.platform'));
        if(!in_array($platform, $val_platform)){
            $data = BaseController::setResponse('INVALID_PLATFORM');
        }

        return $data;
    }

    public static function VerifyAPIKEY(Request $request)
    {
        $data = array();       
        $api_key = $request->header('CPNYAPIKEY');
        $company = Company::select('api_key')->first();
        $validate_key = '';       

        if(isset($company['api_key'])){
            $validate_key = $company['api_key'];
        }
        if(!$api_key || !$validate_key || ($api_key != $validate_key)){
            $data = BaseController::setResponse('INVALID_API_KEY');
        } 

        return $data;        
    }

    public static function VeriFyUserAccount($request)
    {
        $data = array(); 

        //pr(Auth::guest());
        if(!Auth::guest()){

             //pr(Auth::guest());
            // $userdata = DB::table('users')->where('id', @$userid)->first();            
            // if(empty($userdata)) {
            //     $data = BaseController::setResponse('VALIDATION_ERROR', __('messages.invalid_user'));
            // } elseif ($userdata->status!='1') {
            //     $data = BaseController::setResponse('ACCOUNT_DISABLED');
            // } elseif ($userdata->status!='1') {
            //     $data = BaseController::setResponse('LICENSE_EXPIRED');
            // } else {
            //     $id = (new Parser())->parse($token)->getHeader('jti');
            //     $tokenExpiryTime = DB::table('oauth_access_tokens')->where('id', $id)->first()->expires_at;
            //     if (Carbon::parse($tokenExpiryTime) < Carbon::now()) {
            //         $data = BaseController::setResponse('SESSION_EXPIRED');
            //     }
            // }
            //pr($data); 
        }
        return $data;
    }
}
