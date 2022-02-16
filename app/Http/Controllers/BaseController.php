<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Traits\CommonTrait;
use App\Traits\HasPermissionsTrait;
use App\Traits\StoreImageTrait;
use App\Models\Company;
use App\Models\Media;
use Illuminate\Support\Facades\Mail;
/* 
Created By Rahul
Date : 31 Dec 2021 
Base Controller
*/
class BaseController extends Controller
{
    use CommonTrait, HasPermissionsTrait, StoreImageTrait;
    /**
     @OA\Info(
         description="L5 Swagger OpenApi description",
         version="1.0.0",
         title="ProjectName OpenApi Documentation",
    )

    @OA\Server(
        url=L5_SWAGGER_CONST_HOST_V1,
        description="ProjectName V1"
    )     
    */
    /**
       @OA\SecurityScheme(
           type="http",
           description="Login with email and password to get the authentication token",
           name="Token based Based",
           in="header",
           scheme="bearer",
           bearerFormat="JWT",
           securityScheme="apiAuth",
       ),
    */ 

    public static function setResponse($type, $message=[], $result = [])
    {
        switch (strtoupper($type)) {
            case 'SUCCESS':
                $code = 200;
                $default_message['NOTIFICATION'] = $message;
                break;
            case 'INVALID_API_KEY':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.INVALID_API_KEY')];
                break;
            case 'INVALID_PLATFORM':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.INVALID_PLATFORM')];
                break;
            case 'LOGIN_FAILED':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.LOGIN_FAILED')];
                break;
            case 'AUTH_FAILED':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.AUTH_FAILED')];
                break;
            case 'SESSION_EXPIRED':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.SESSION_EXPIRED')];
                break;
            case 'ACCOUNT_DISABLED':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.ACCOUNT_DISABLED')];
                break;
            case 'LICENSE_EXPIRED':
                $code = 403;
                $default_message['NOTIFICATION'] = [__('api.notifications.LICENSE_EXPIRED')];
                break;
            case 'VALIDATION_ERROR':
                $code = 422;
                $default_message = $message;
                break;
            case 'OTHER_ERROR':
                $code = 423;
                $default_message['NOTIFICATION'] = $message;
                break;
            case 'INVALID_URL':
                $code = 404;
                $default_message['NOTIFICATION'] = [__('api.notifications.INVALID_URL')];
                break;
            case 'EXCEPTION':
                $code = '';
                $default_message['NOTIFICATION'] = $message;
                break;
            default:
                break;
        }

        $data = [];
        $data['STATUS'] = $default_message;
        if(!empty($result)){
            $data = array_merge($data,$result);
        }
        return ['data' => $data,'code' => $code];
    }


    public function sendResponse($message, $result)
    {
        $response = [
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    protected function respondWithToken($token, $data = [])
    {
        return response()->json([
            'data' => $data,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard()->factory()->getTTL()
        ]);
    }

    public function sendEmail($to,$subject,$body){
        $company = Company::select('email_header_id','theme_color','email_footer')->first();
        //pr($company);
        $header_image_url = Media::where('id',$company->email_header_id)->value('url');
        //$header_image_url = $this->downloadFileS3($header_image_url);
        $theme_color = $company->theme_color;
        
        $details = [
            'theme_color' => $theme_color,
            'header_img' => @$header_image_url,
            'footer' => $company->email_footer
        ];        
        // $details = [
        //     'msg_title' => 'Mail from testing.com',
        //     'msg_body' => 'This is for testing email using smtp',
        //     'theme_color' => $theme_color,
        //     'header_img' => @$header_image_url,
        //     'footer' => $company->email_footer
        // ];       
        // \Mail::to('rahulpatel@siliconithub.com')->send(new \App\Mail\MyTestMail($details,$subject));       
        // pr("Email is Sent.");                           
        // //$mail = \Mail::to('rahulpatel@siliconithub.com')->send(new \App\Mail\Forgotpassword($details,$body,$subject));      
        
        // pr($mail);
        $mail = Mail::send('emails.email',[
                    "details" => $details,
                    "msg_body" => $body
                ], function($message) use($to,$subject) {
                    $message->to($to);
                    $message->subject($subject);
                });
        return $mail;
    }
}
