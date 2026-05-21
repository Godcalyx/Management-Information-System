<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SecuritySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run()
{
    $defaults = [
        'security_min_password_length' => 0,
        'security_require_special_char' => true,
        'security_session_timeout' => 30,
    ];

    foreach ($defaults as $key => $value) {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}

}
