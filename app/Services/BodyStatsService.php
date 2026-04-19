<?php

namespace App\Services;

use App\Models\BodyStat;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BodyStatsService
{
    /**
     * 進捗グラフ用データを一括取得
     * - 実績データ（実線）
     * - 未来予測データ（点線）
     * - 目標達成予定日
     */
    public function getProgressData(Client $client): array
    {
        $stats = BodyStat::where('client_id', $client->id)
            ->orderBy('measured_at')
            ->get();

        if ($stats->isEmpty()) {
            return $this->emptyProgressData();
        }

        $actual   = $this->buildActualPoints($stats);
        $rate     = $this->calcDailyRate($stats);
        $forecast = $this->buildForecastPoints($stats->last(), $client, $rate);
        $date     = $this->calcPredictedDate($stats->last(), $client, $rate);

        return [
            'actual'          => $actual,
            'forecast'        => $forecast,
            'predicted_date'  => $date,
            'daily_rate_g'    => $rate !== null ? round($rate * 1000, 1) : null, // g/日
            'latest_weight'   => $stats->last()->weight,
            'latest_measured' => $stats->last()->measured_at->format('Y-m-d'),
            'total_records'   => $stats->count(),
        ];
    }

    /**
     * 実績グラフポイント生成
     */
    private function buildActualPoints(Collection $stats): array
    {
        return $stats->map(fn(BodyStat $s) => [
            'x'           => $s->measured_at->format('Y-m-d'),
            'y'           => $s->weight,
            'body_fat'    => $s->body_fat_percentage,
            'muscle_mass' => $s->muscle_mass,
            'bmi'         => $s->bmi,
        ])->values()->toArray();
    }

    /**
     * 1日あたりの平均減量ペース算出（kg/日）
     * 過去4週間のデータを使用
     */
    private function calcDailyRate(Collection $stats): ?float
    {
        $recent = $stats->filter(fn(BodyStat $s) =>
            $s->measured_at->gte(now()->subWeeks(4))
        );

        // データが2件未満の場合は全期間で算出
        if ($recent->count() < 2) {
            $recent = $stats;
        }

        if ($recent->count() < 2) {
            return null;
        }

        $first = $recent->first();
        $last  = $recent->last();
        $days  = $first->measured_at->diffInDays($last->measured_at);

        if ($days === 0) {
            return null;
        }

        $rate = ($first->weight - $last->weight) / $days;

        // 減量していない場合（増量・維持）は予測線を出さない
        return $rate > 0 ? $rate : null;
    }

    /**
     * 未来予測グラフポイント生成
     * 週1回のペースで目標体重まで点を打つ
     */
    private function buildForecastPoints(
        BodyStat $latest,
        Client $client,
        ?float $rate
    ): array {
        if ($rate === null || $client->target_weight === null) {
            return [];
        }

        $points        = [];
        $currentDate   = Carbon::parse($latest->measured_at);
        $currentWeight = $latest->weight;
        $targetWeight  = $client->target_weight;
        $maxWeeks      = 104; // 最大2年分

        // 起点（最後の実績点）は含めない
        $currentDate   = $currentDate->addWeeks(1);
        $currentWeight -= $rate * 7;

        while ($currentWeight > $targetWeight && count($points) < $maxWeeks) {
            $points[] = [
                'x' => $currentDate->format('Y-m-d'),
                'y' => round(max($currentWeight, $targetWeight), 2),
            ];
            $currentDate    = $currentDate->copy()->addWeeks(1);
            $currentWeight -= $rate * 7;
        }

        // 目標体重到達点を最後に追加
        if (! empty($points)) {
            $points[] = [
                'x' => $currentDate->format('Y-m-d'),
                'y' => $targetWeight,
            ];
        }

        return $points;
    }

    /**
     * 目標達成予定日を算出
     */
    private function calcPredictedDate(
        BodyStat $latest,
        Client $client,
        ?float $rate
    ): ?string {
        if ($rate === null || $client->target_weight === null) {
            return null;
        }

        $remaining  = $latest->weight - $client->target_weight;

        if ($remaining <= 0) {
            return '達成済み';
        }

        $daysNeeded = (int) ceil($remaining / $rate);

        return Carbon::parse($latest->measured_at)
            ->addDays($daysNeeded)
            ->format('Y年m月d日');
    }

    /**
     * BMI推移データ（グラフ用）
     */
    public function getBmiHistory(Client $client): array
    {
        return BodyStat::where('client_id', $client->id)
            ->orderBy('measured_at')
            ->get(['measured_at', 'bmi'])
            ->map(fn(BodyStat $s) => [
                'x' => $s->measured_at->format('Y-m-d'),
                'y' => $s->bmi,
            ])
            ->toArray();
    }

    /**
     * 筋肉量推移データ（グラフ用）
     */
    public function getMuscleMassHistory(Client $client): array
    {
        return BodyStat::where('client_id', $client->id)
            ->orderBy('measured_at')
            ->whereNotNull('muscle_mass')
            ->get(['measured_at', 'muscle_mass'])
            ->map(fn(BodyStat $s) => [
                'x' => $s->measured_at->format('Y-m-d'),
                'y' => $s->muscle_mass,
            ])
            ->toArray();
    }

    /**
     * データなし時のデフォルト
     */
    private function emptyProgressData(): array
    {
        return [
            'actual'          => [],
            'forecast'        => [],
            'predicted_date'  => null,
            'daily_rate_g'    => null,
            'latest_weight'   => null,
            'latest_measured' => null,
            'total_records'   => 0,
        ];
    }
}