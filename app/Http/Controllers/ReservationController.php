<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Seat;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // 一覧画面 兼 検索機能
    public function index(Request $request)
    {
        // 予約と紐づく席情報をあわせて取得 (N+1問題対策)
        $query = Reservation::with('seats');

        // 1. 日付で検索
        if ($request->filled('reservation_date')) {
            $query->where('reservation_date', $request->reservation_date);
        }

        // 2. 氏名で検索（部分一致）
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        // 3. 人数で検索
        if ($request->filled('people')) {
            $query->where('people', '>=', $request->people);
        }

        // 4. ステータスで検索 (temporary, reserved, cancelled)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. 席で検索（中間テーブルを跨いだリレーション検索）
        if ($request->filled('seat_id')) {
            $query->whereHas('seats', function ($q) use ($request) {
                $q->where('seats.id', $request->seat_id);
            });
        }

        // ページネーション（1ページあたり10件表示）を適用して並び替え
        $reservations = $query->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10)
            ->appends($request->query()); // ページネーション時の検索条件維持

        // 検索フォームのプルダウン用に全座席データも取得
        $seats = Seat::all();

        return view('reservations.index', compact('reservations', 'seats'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}