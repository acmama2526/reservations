<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

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

    // 2. 席（seats）テーブルとの多対多のリレーション定義
    // (1つの予約に複数の席を紐づけられるようにするため)
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'reservation_seat');
    }
}
