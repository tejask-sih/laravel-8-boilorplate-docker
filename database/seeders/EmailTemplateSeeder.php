<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        $forgot_email_body = '<p>Hello ##NAME##, </p><p>Someone has requested to reset your password. If you have requested this, you can change the password using below OTP.</p><h2> ##OTP## </h2><p>If you didn’t request this, you can ignore this email.</p><p>Your password won’t be changed until you reset the password using above OTP. </p>';

        $reset_email_body = '<p>Hello ##NAME##, </p><p>Your account password is successfully changed to </p><h2> ##PASSWORD## </h2>';

        $email_template = [
            '1' => [
                'id' => '1', 
                'purpose' => 'forgot password', 
                'subject' => 'You have requested to reset your password', 
                'body' => $forgot_email_body, 
                'status' => 0, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
            '2' => [
                'id' => '2', 
                'purpose' => 'reset password', 
                'subject' => 'Your account password is successfully changed', 
                'body' => $reset_email_body, 
                'status' => 0, 
                'created_at' => $now, 
                'updated_at' => $now
            ]
        ];

        foreach ($email_template as $id => $row) {
            EmailTemplate::firstOrCreate(
                ['id' => $id ],
                $row
            );
        }
    }
}
