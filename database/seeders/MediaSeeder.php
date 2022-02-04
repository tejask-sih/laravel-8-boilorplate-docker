<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        $ref_media = [
            '1' => ['id' => '1','storage' => 'Amazon S3','url' => 'branding/logo.png','mime_type' => 'image','created_at' => $now],
            '2' => ['id' => '2','storage' => 'Amazon S3','url' => 'branding/hero_image.jpg','mime_type' => 'image','created_at' => $now],
            '3' => ['id' => '3','storage' => 'Amazon S3','url' => 'avatars/default.png','mime_type' => 'image','created_at' => $now],
            '4' => ['id' => '4','storage' => 'Amazon S3','url' => 'branding/email_header.png','mime_type' => 'image','created_at' => $now],
            '5' => ['id' => '5','storage' => 'Amazon S3','url' => 'branding/favicon.png','mime_type' => 'image','created_at' => $now] 
            ];

        foreach ($ref_media as $id => $row) {
            Media::firstOrCreate(
                ['id' => $id ],
                $row
            );
        }
    }
}
