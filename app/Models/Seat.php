<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'seat_name',
        'type',
        'capacity',
        'is_active',
    ];

    // 予約（reservations）との多対多のリレーション定義
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_seat');
    }
}
