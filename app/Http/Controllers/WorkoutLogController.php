<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Menu;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;

class WorkoutLogController extends Controller
{
    // トレーニング入力画面
    public function create(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $menus = Menu::orderBy('category')->orderBy('name')->get()
            ->groupBy('category');

        $latestLogs = WorkoutLog::with('menu')
            ->where('client_id', $client->id)
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('workout_logs.create', compact('client', 'menus', 'latestLogs'));
    }

    // トレーニング記録保存
    public function store(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $validated = $request->validate([
            'logs'                    => 'required|array|min:1',
            'logs.*.menu_id'          => 'required|exists:menus,id',
            'logs.*.weight'           => 'required|numeric|min:0|max:500',
            'logs.*.reps'             => 'required|integer|min:1|max:200',
            'logs.*.sets'             => 'required|integer|min:1|max:20',
            'logs.*.intensity'        => 'required|in:1,2,3',
            'logs.*.condition_notes'  => 'nullable|string|max:500',
            'logged_at'               => 'required|date',
        ]);

        foreach ($validated['logs'] as $log) {
            WorkoutLog::create([
                'client_id'       => $client->id,
                'menu_id'         => $log['menu_id'],
                'weight'          => $log['weight'],
                'reps'            => $log['reps'],
                'sets'            => $log['sets'],
                'intensity'       => $log['intensity'],
                'condition_notes' => $log['condition_notes'] ?? null,
                'logged_at'       => $validated['logged_at'],
            ]);
        }

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'トレーニングを記録しました。');
    }

    // 削除
    public function destroy(Request $request, Client $client, WorkoutLog $workoutLog)
    {
        $this->authorizeClient($request, $client);
        abort_if($workoutLog->client_id !== $client->id, 403);
        $workoutLog->delete();

        return back()->with('success', '記録を削除しました。');
    }

    private function authorizeClient(Request $request, Client $client): void
    {
        $user = $request->user();
        if ($user->isTrainer() && $client->trainer_id !== $user->id) {
            abort(403);
        }
    }
}