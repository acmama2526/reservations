<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seat extends Model
{
  // データベースに登録・更新するときに使用してよい項目を指定する
  protected $fillable = [
    'seat_name',
    'type',
    'capacity',
    'is_active',
    'display_order',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  // この席と「予約」の関係を定義する
  public function reservations(): BelongsToMany
  {
    return $this->belongsToMany(
      Reservation::class,
      'reservation_seat'
    )->withTimestamps();
  }
}
