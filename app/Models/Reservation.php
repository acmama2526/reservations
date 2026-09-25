<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    // フォームから一括で保存・更新を許可するカラム
    protected $fillable = [
        'customer_name',
        'people',
        'reservation_date',
        'start_time',
        'end_time',
        'phone',
        'status',
        'description',
    ];

    protected $casts = [
        'reservation_date' => 'date',
    ];

    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(
            Seat::class,
            'reservation_seat'
        )->withTimestamps();
    }
}
