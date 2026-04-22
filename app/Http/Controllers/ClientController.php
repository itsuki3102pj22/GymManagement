<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Services\BodyStatsService;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function __construct(
        private BodyStatsService $bodyStats,
        private NutritionService $nutrition,
    ) {}

    // 顧客一覧
    public function index(Request $request)
    {
        $user = $request->user();

        $clients = $user->isSupervisor()
            ? Client::with('trainer')->latest()->paginate(20)
            : Client::where('trainer_id', $user->id)->latest()->paginate(20);

        return view('clients.index', compact('clients', 'user'));
    }

    // 顧客詳細・進捗
    public function show(Request $request, Client $client)
    {
        $this->authorizeClient($request->user(), $client);

        $progress    = $this->bodyStats->getProgressData($client);
        $latestStat  = $client->latestBodyStat;
        $eer         = $this->nutrition->calcEer($client);
        $bmiData     = null;
        $targetRange = ['min' => 0, 'max' => 0];

        if ($latestStat) {
            $bmi     = $this->nutrition->calcBmi($latestStat->weight, $client->height);
            $bmiData = $this->nutrition->bmiStatus($bmi, $client->age);
        }

        // 目標BMI体重範囲を計算
        if ($bmiData) {
            $heightM = $client->height / 100;
            $targetRange = [
                'min' => round($bmiData['target_min'] * ($heightM ** 2), 1),
                'max' => round($bmiData['target_max'] * ($heightM ** 2), 1),
            ];
        }

        // 最近のトレーニングログを取得
        $recentWorkouts = $client->workoutLogs()
            ->with('menu')
            ->latest('logged_at')
            ->limit(10)
            ->get();

        return view('clients.show', compact(
            'client', 'progress', 'latestStat', 'eer', 'bmiData', 'targetRange', 'recentWorkouts'
        ));
    }

    // 顧客登録フォーム
    public function create()
    {
        return view('clients.create');
    }

    // 顧客登録処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'height'        => 'required|numeric|min:100|max:250',
            'gender'        => 'required|in:1,2',
            'birth_date'    => 'required|date|before:today',
            'pal_level'     => 'required|in:1,2,3',
            'target_weight' => 'nullable|numeric|min:30|max:200',
            'medical_notes' => 'nullable|string',
        ]);

        $validated['trainer_id'] = $request->user()->id;
        $validated['uuid']       = (string) Str::uuid();

        $client = Client::create($validated);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', "「{$client->name}」さんを登録しました。");
    }

    // 顧客編集フォーム
    public function edit(Request $request, Client $client)
    {
        $this->authorizeClient($request->user(), $client);
        return view('clients.edit', compact('client'));
    }

    // 顧客更新処理
    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($request->user(), $client);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'height'        => 'required|numeric|min:100|max:250',
            'gender'        => 'required|in:1,2',
            'birth_date'    => 'required|date|before:today',
            'pal_level'     => 'required|in:1,2,3',
            'target_weight' => 'nullable|numeric|min:30|max:200',
            'medical_notes' => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $client->update($validated);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', '顧客情報を更新しました。');
    }

    // 担当権限チェック
    private function authorizeClient(\App\Models\User $user, Client $client): void
    {
        if (! $user->isSupervisor() && $client->trainer_id !== $user->id) {
            abort(403, 'この顧客データへのアクセス権限がありません。');
        }
    }
}