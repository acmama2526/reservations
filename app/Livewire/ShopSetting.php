<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ShopSetting as ShopSettingModel;

class ShopSetting extends Component
{
    public $shop_name = '';

    public $business_start = '';

    public $business_end = '';

    public $slot_minutes = 15;

    public $closed_days = [];

    public function mount()
    {
        $setting = ShopSettingModel::first();

        if ($setting) {
            $this->shop_name = $setting->shop_name;
            $this->business_start = $setting->business_start;
            $this->business_end = $setting->business_end;
            $this->slot_minutes = $setting->slot_minutes;
            $this->closed_days = $setting->closed_days;
        }
    }

    public function render()
    {
        return view('livewire.shop-setting');
    }
}