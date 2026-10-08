<?php

namespace App\Support;

use App\Models\ShopSetting;
use Carbon\CarbonImmutable;

class LoginExpiry
{
    public static function deadline(): CarbonImmutable
    {
        $now = CarbonImmutable::now(
            config('login-expiry.timezone', 'Asia/Tokyo')
        );

        $midnight = $now->startOfDay()->addDay();

        $setting = ShopSetting::find(1);

        $start = substr(
            (string) $setting?->getRawOriginal('business_start'),
            0,
            5
        );

        $end = substr(
            (string) $setting?->getRawOriginal('business_end'),
            0,
            5
        );

        // 営業時間が未設定の場合の暫定期限。
        $timePattern = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';

        if (
            ! preg_match($timePattern, $start)
            || ! preg_match($timePattern, $end)
        ) {
            return $now->addDay();
        }

        $opens = $now->setTimeFromTimeString($start);
        $closes = $now->setTimeFromTimeString($end);

        // 日をまたぐ営業にも対応。
        if ($closes->lessThanOrEqualTo($opens)) {
            $closes = $closes->addDay();
        }

        $deadline = $closes->addMinutes(
            (int) config('login-expiry.closing_grace_minutes', 30)
        );

        $previousDeadline = $deadline->subDay();

        // 前営業日の営業中・猶予時間内なら、その期限を使う。
        if (
            $now->lessThan($opens)
            && $now->lessThan($previousDeadline)
        ) {
            $deadline = $previousDeadline;
        }

        // 閉店後に明示的にログインし直すことは許可する。
        if ($deadline->lessThanOrEqualTo($now)) {
            $deadline = $deadline->addDay();
        }

        if (config('login-expiry.logout_at_midnight', false)) {
            return $deadline->lessThan($midnight)
                ? $deadline
                : $midnight;
        }

        return $deadline;
    }
}