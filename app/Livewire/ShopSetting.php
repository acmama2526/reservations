<?php

namespace App\Livewire;

use App\Models\ShopSetting as ShopSettingModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShopSetting extends Component
{
    public $shop_name = '';
    public $business_start = '';
    public $business_end = '';
    public $slot_minutes = 15;
    public $closed_days = [];

    public function mount()
    {
        // 保存するときと同じレコードを読み込む
        $setting = ShopSettingModel::find(1);

        if ($setting) {
            $this->shop_name = $setting->shop_name;
            $this->business_start = substr((string) $setting->business_start, 0, 5);
            $this->business_end = substr((string) $setting->business_end, 0, 5);
            $this->slot_minutes = (int) $setting->slot_minutes;

            $days = $setting->closed_days;

            if (is_string($days)) {
                $days = json_decode($days, true);
            }

            $this->closed_days = is_array($days) ? $days : [];
        }
    }

    private function canEdit(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function save()
    {
        $this->resetValidation();

        if (! $this->canEdit()) {
            $this->addError(
                'permission',
                '店舗設定を変更する権限がありません。'
            );

            return;
        }

        $data = $this->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'business_start' => ['required', 'date_format:H:i'],
            'business_end' => ['required', 'date_format:H:i'],
            'slot_minutes' => ['required', 'integer', 'in:15,30,60'],
            'closed_days' => ['array'],
            'closed_days.*' => [
                'in:月曜日,火曜日,水曜日,木曜日,金曜日,土曜日,日曜日,祝日',
            ],
        ], [
            'shop_name.required' => '店舗名を入力してください。',
            'shop_name.max' => '店舗名は255文字以内で入力してください。',
            'business_start.required' => '開店時間を入力してください。',
            'business_start.date_format' => '開店時間を正しく入力してください。',
            'business_end.required' => '閉店時間を入力してください。',
            'business_end.date_format' => '閉店時間を正しく入力してください。',
            'slot_minutes.in' => '表示単位は15分・30分・60分から選択してください。',
        ]);

        ShopSettingModel::updateOrCreate(['id' => 1], $data);

        session()->flash('message', '保存しました');
    }

    public function render()
    {
        return view('livewire.shop-setting', [
            'canEdit' => $this->canEdit(),
        ]);
    }
}