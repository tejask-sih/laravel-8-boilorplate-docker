<?php

if (! function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }
}

if(!function_exists('send_json_response'))
{
    function send_json_response($request, $response)
    {
        //pr($response);
        addLog($request, $response);
        return response()->json($response['data'],$response['code']);
        // return response($response, $code);
    }
}

if (! function_exists('hoursandmins')) {
    function hoursandmins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
}

if(!function_exists('pr'))
{
    function pr($data="")
    {    
       $result = print_r($data); exit();
       return  $result;  
    }
}

if(!function_exists('addLog')){
    function addLog($request='', $response='')
    {
        $systemLog = DB::table('sys_audit_log')->insert([
            'user_id' => @Auth::guard()->user()->id,
            'session_id' => NULL,  
            'api' => $request->url(),
            'request' => json_encode(['method'=>$request->method(),'Time'=> date('Y-m-d H:i:s'),'url'=>$request->url(),'Request Headers'=> json_encode(collect($request->header())->toArray()),'GET Request'=> json_encode($request->query()),'POST Request'=> json_encode($request->post())]),
            'response' => json_encode($response),
        ]);
        
        $cyd    = date('Y/m');       
        $cf     = 'api-log/'.$cyd; 
        $fname  = $cf.'/logs-'.date('Y-m-d').'.html';
       
        if(!is_dir($cf)) {  @mkdir($cf, 0777, true); }       
        
        if(!file_exists($fname)){
            $html="<!DOCTYPE html>
                    <html>
                        <head>
                            <meta name='viewport' content='width=device-width, initial-scale=1'>
                            <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'>
                            <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
                            <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js'></script>
                            <style>
                                body{ font-size:16px; }
                                .main_div { background: #efefef;border-bottom: 4px solid #D32121;box-shadow: 0 -1px 2px #B89595; }
                                .request_div { background:#93E0EA;padding:10px 10px; }
                                .req_span{ background: #93EAA8; }
                                .req_para { background: #93E0EA;margin-top:10px;margin-bottom:5px; overflow-wrap: break-word;}
                                .res{ background: #93EAA8;padding: 10px; overflow-wrap: break-word;}
                            </style>
                        </head>
                        <body>
                            <div class='container-flude'>
                            </div>
                        </body>
                    </html>";
            file_put_contents($fname, $html.PHP_EOL , FILE_APPEND | LOCK_EX);
        }
        $lines = array();
        $html1='
            <br>
            <div class="main_div">
                <div class="request_div">
                    <b>Request :</b>
                    <span class="req_span">'.$request->method().' Method</span>
                    <b> Time </b> : <span class="req_span">'.date('Y-m-d H:i:s').'</span>
                    <b>URL</b> :  <span class="req_span">'.$request->url().'</span>
                    <div class="req_para"><b>Request Headers</b> ------- : '.json_encode(collect($request->header())->toArray()).'</div>
                    <div class="req_para"><b>GET Request</b> ------- : '.json_encode($request->query()).'</div>
                    <div class="req_para"><b>POST Request</b> ------- : '.json_encode($request->post()).'</div>
                </div>
                <br>
                <div class="res">
                    <b>Json Response </b> -------<br>'.json_encode($response).'
                </div>
            </div>';
        $html1.="\n";
        foreach(file($fname) as $line) {
            array_push($lines, $line);
            if(strpos($line, "<div class='container-flude'>") !== FALSE){ array_push($lines, $html1); }
        }
        $myfile = file_put_contents($fname, $lines);
    }
}