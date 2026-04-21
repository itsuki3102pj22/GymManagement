<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 責任者は全顧客、トレーナーは担当顧客のみ
        $clientQuery = $user->isSupervisor()
            ? Client::query()
            : Client::where('trainer_id', $user->id);

        // 本日の予約
        $todayReservations = Reservation::with(['client', 'trainer'])
            ->whereDate('start_at', today())
            ->when(! $user->isSupervisor(), fn($q) =>
                $q->where('trainer_id', $user->id)
            )
            ->orderBy('start_at')
            ->get();

        // サマリー数値
        $stats = [
            'total_clients'  => $clientQuery->count(),
            'active_clients' => (clone $clientQuery)->where('is_active', true)->count(),
            'today_sessions' => $todayReservations->count(),
            'new_this_month' => (clone $clientQuery)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('dashboard', compact('stats', 'todayReservations', 'user'));
    }
}