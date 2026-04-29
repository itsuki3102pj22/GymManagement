<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FoodLog;
use App\Models\FoodMaster;

class LineFoodLogService
{
    public function __construct(
        private NutritionService $nutrition
    ) {}

    /**
     * LINEの食事テキストを解析してfood_logsに保存
     */
    public function handle(Client $client, string $text): array
    {
        // food_masterから食品名を検索してマッチング
        $foods     = FoodMaster::all();
        $matched   = collect();
        $totalCal  = 0;
        $totalP    = 0;
        $totalF    = 0;
        $totalC    = 0;

        foreach ($foods as $food) {
            // テキスト内に食品名が含まれているか確認
            if (mb_strpos($text, $food->food_name) !== false) {
                // 数量パターンを検出（例：「2個」「1杯」「100g」）
                $quantity = $this->extractQuantity($text, $food->food_name);
                $matched->push([
                    'name'     => $food->food_name,
                    'calories' => $food->calories * $quantity,
                    'protein'  => $food->protein  * $quantity,
                    'fat'      => $food->fat       * $quantity,
                    'carb'     => $food->carb      * $quantity,
                ]);
                $totalCal += $food->calories * $quantity;
                $totalP   += $food->protein  * $quantity;
                $totalF   += $food->fat      * $quantity;
                $totalC   += $food->carb     * $quantity;
            }
        }

        // PFC比率を計算
        $pBalance = $totalCal > 0
            ? round(($totalP * 4 / $totalCal) * 100, 1) : 0;
        $fBalance = $totalCal > 0
            ? round(($totalF * 9 / $totalCal) * 100, 1) : 0;
        $cBalance = $totalCal > 0
            ? round(($totalC * 4 / $totalCal) * 100, 1) : 0;

        // food_logsに保存
        FoodLog::create([
            'client_id'      => $client->id,
            'meal_text'      => $text,
            'total_calories' => (int) round($totalCal),
            'p_balance'      => $pBalance,
            'f_balance'      => $fBalance,
            'c_balance'      => $cBalance,
            'logged_at'      => today(),
        ]);

        \Log::info('LINE food log saved', [
            'client_id' => $client->id,
            'calories'  => (int) round($totalCal),
        ]);

        // EERとの比較
        $eer    = $this->nutrition->calcEer($client);
        $pfcOk  = $this->nutrition->pfcStatus($pBalance, $fBalance, $cBalance);

        return [
            'calories' => (int) round($totalCal),
            'protein'  => round($totalP, 1),
            'fat'      => round($totalF, 1),
            'carb'     => round($totalC, 1),
            'p_balance' => $pBalance,
            'f_balance' => $fBalance,
            'c_balance' => $cBalance,
            'matched'  => $matched,
            'eer'      => $eer,
            'pfc_ok'   => $pfcOk,
        ];
    }

    /**
     * テキストから数量を推定（例：「2個」→2.0）
     */
    private function extractQuantity(string $text, string $foodName): float
    {
        // 食品名の前後の数字を検索
        $pattern = '/([0-9０-９.．]+)\s*(個|杯|枚|本|切|人前|g|グラム|ml|cc)?/u';
        if (preg_match($pattern, $text, $matches)) {
            $num = mb_convert_kana($matches[1], 'n');
            return max(0.1, (float) $num);
        }
        return 1.0;
    }
}
