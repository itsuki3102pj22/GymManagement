<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // カレンダー表示
    public function index(Request $request)
    {
        $user = $request->user();

        // 表示週の起点（デフォルト：今週月曜）
        $weekStart = $request->filled('week')
            ? \Carbon\Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        $reservations = Reservation::with(['client', 'trainer'])
            ->whereBetween('start_at', [$weekStart, $weekEnd])
            ->when($user->isTrainer(), fn($q) =>
                $q->where('trainer_id', $user->id)
            )
            ->orderBy('start_at')
            ->get();

        // カレンダー用に日付をキーにグループ化
        $calendarData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $calendarData[$date->format('Y-m-d')] = [
                'date'         => $date,
                'reservations' => $reservations->filter(fn($r) =>
                    $r->start_at->format('Y-m-d') === $date->format('Y-m-d')
                )->values(),
            ];
        }

        $clients  = Client::where('is_active', true)
            ->when($user->isTrainer(), fn($q) =>
                $q->where('trainer_id', $user->id)
            )
            ->orderBy('name')->get();

        $trainers = User::where('role', 1)->orderBy('name')->get();

        return view('reservations.index', compact(
            'calendarData', 'weekStart', 'weekEnd', 'clients', 'trainers'
        ));
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

    // 予約作成
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'  => 'required|exists:clients,id',
            'trainer_id' => 'required|exists:users,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'status'     => 'required|in:0,1,2',
        ]);

        $startAt = $validated['date'] . ' ' . $validated['start_time'];
        $endAt   = $validated['date'] . ' ' . $validated['end_time'];

        // 同一トレーナーの時間帯重複チェック
        $overlap = Reservation::where('trainer_id', $validated['trainer_id'])
            ->where('status', '!=', 2)
            ->where(function ($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                  ->orWhereBetween('end_at', [$startAt, $endAt])
                  ->orWhere(function ($q2) use ($startAt, $endAt) {
                      $q2->where('start_at', '<=', $startAt)
                         ->where('end_at', '>=', $endAt);
                  });
            })->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->with('error', 'その時間帯はすでに予約が入っています。');
        }

        Reservation::create([
            'client_id'  => $validated['client_id'],
            'trainer_id' => $validated['trainer_id'],
            'start_at'   => $startAt,
            'end_at'     => $endAt,
            'status'     => $validated['status'],
        ]);

        return back()->with('success', '予約を登録しました。');
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
    // ステータス更新
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        $reservation->update($validated);

        return back()->with('success', 'ステータスを更新しました。');
    }


    /**
     * Remove the specified resource from storage.
     */
    // 削除
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', '予約を削除しました。');
    }
}
