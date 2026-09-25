<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopSetting;

class ShopSettingSeeder extends Seeder
{
    public function run(): void
    {
        ShopSetting::create([
            'shop_name' => 'bistro Bucchi',
            'business_start' => '12:00:00',
            'business_end' => '22:00:00',
            'slot_minutes' => 15,
            'closed_days' => ['水曜日'],
        ]);
    }
}
