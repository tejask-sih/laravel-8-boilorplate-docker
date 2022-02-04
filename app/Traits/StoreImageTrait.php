<?php 


namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Http\Response;
use Storage;
use Config;

trait StoreImageTrait
{
	/**
     * Download Private folder file 
     */
    public function downloadFileS3($filename)
    {
        $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
        $bucket = Config::get('filesystems.disks.s3.bucket');

        $command = $client->getCommand('GetObject',[
            'Bucket' => $bucket,
            'Key'    => $filename    // file name in s3 bucket which you want to access
        ]);

        $request = $client->createPresignedRequest($command, '+60 minutes');

        // Get the actual presigned-url
        return  (string)$request->getUri();
    }
}


?>