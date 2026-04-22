<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;

class SupervisorController extends Controller
{
    public function index()
    {
        $stats = [
            'total_trainers'  => User::where('role', 1)->count(),
            'total_clients'   => Client::count(),
            'active_clients'  => Client::where('is_active', true)->count(),
            'monthly_revenue' => Payment::where('status', 'succeeded')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
        ];

        $trainers = User::where('role', 1)
            ->withCount('clients')
            ->get();

        return view('supervisor.index', compact('stats', 'trainers'));
    }
}