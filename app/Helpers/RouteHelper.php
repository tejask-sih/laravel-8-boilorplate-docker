<?php


if (! function_exists('include_route_files')) {   
    function include_route_files($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if(!function_exists('send_response'))
{
    function send_response($request, $response)
    {
        addLog($request, $response);
        return response()->json($response['data'],$response['code']);
        // return response($response, $code);
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
        $cyd    = date('Y/m');       
        $cf     = 'public/api-log/'.$cyd; 
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


?>