<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\BodyStatsService;
use App\Services\NutritionService;
use Illuminate\Http\Request;

class PublicProgressController extends Controller
{
    public function __construct(
        private BodyStatsService $bodyStats,
        private NutritionService $nutrition,
    ) {}

    public function show(string $uuid)
    {
        // UUIDで顧客を特定
        $client = Client::where('uuid', $uuid)
        ->where('is_active', true)
        ->firstOrFail();

        // 進捗グラフデータ
        $progress = $this->bodyStats->getProgressData($client);

        // BMI・EER算出
        $latestStat = $client->latestBodyStat;
        $bmiData = null;
        $eer = $this->nutrition->calcEer($client);

        if ($latestStat) {
            $bmi = $this->nutrition->calcBmi(
                $latestStat->weight,
                $client->height
            );
            $bmiData = $this->nutrition->bmiStatus($bmi, $client->age());
        }

        // 目標体重の範囲
        $targetRange = $this->nutrition->targetWeightRange(
            $client->height,
            $client->age
        );

        // 最新PFCバランス
        $latestFood = $client->foodLogs()->latest('logged_at')->first();
        $pfcStatus = null;
        if ($latestFood) {
            $pfcStatus = $this->nutrition->pfcStatus(
                $latestFood->p_balance ?? 0,
                $latestFood->f_balance ?? 0,
                $latestFood->c_balance ?? 0,
            );
        }

        return view('public.progress', compact(
            'client',
            'progress',
            'bmiData',
            'eer',
            'targetRate',
            'latestStat',
            'pfcStatus',
        ));
    }
}