<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'email', 'value' => 'support@bitaqati.com'],
            ['key' => 'phone', 'value' => '+966 12 345 6789'],
            ['key' => 'address', 'value' => 'الرياض، المملكة العربية السعودية'],
        ];

        foreach ($settings as $setting) {
            ContactSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
