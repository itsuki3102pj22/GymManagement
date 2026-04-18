<?php

namespace App\Services;

class NutritionService
{
    // 推定エネルギー必要量（EER）参照表（厚生労働省2025年版）
    // [性別][年齢区分][PALレベル(1-3)] => kcal
    private const EER_TABLE = [
        'meal' => [
            '18-29' => [2300, 2650, 3050],
            '30-49' => [2300, 2700, 3050],
            '50-64' => [2200, 2600, 2950],
            '65-74' => [2050, 2400, 2750],
            '75+' => [1800, 2100, null],
        ],
        'female' => [
            '18-29' => [1700, 2000, 2300],
            '30-49' => [1750, 2050, 2350],
            '50-64' => [1650, 1950, 2250],
            '65-74' => [1550, 1850, 2100],
            '75+' => [1400, 1650, null],
        ],
    ];

    //　BMIの計算
    public function calcBmi(float $weight, float $height_cm): float
    {
        $h = $height_cm / 100;
        return round($weight / ($h * $h), 1);
    }

    //　BMIの判定
    public function bmiStatus(float $bmi): string
    {
        return match(true) {
            $bmi < 18.5 => '低体重',
            $bmi <= 24.9 => '標準（目標範囲）',
            $bmi <= 29.9 => '過体重',
            default => '肥満',
        };
    }
    
    // EERの計算
    public function calcEer(\App\Models\Client $client): ?int
    {
        $age = now()->diffInYears($client->birth_date); // 年齢計算
        $gender = $client->gender === 1 ? 'meal' : 'female'; // 性別判定
        $pal = $client->pal_level - 1;

        // 年齢区分の判定
        $ageKey = match(true) {
            $age < 30 => '18-29',
            $age < 50 => '30-49',
            $age < 65 => '50-64',
            $age < 75 => '65-74',
            default => '75+',
        };

        return self::EER_TABLE[$gender][$ageKey][$pal] ?? null;
    }

    // PFCバランスの判定
    public function pfcStatus(float $p_pct, float $f_pct, float $c_pct): array
    {
        return [
            'protein' => ['value' => $p_pct, 'ok' => $p_pct >= 13 && $p_pct <= 20, 'range' => '13~20%'],
            'fat' => ['value' => $f_pct, 'ok' => $f_pct >= 20 && $f_pct <= 30, 'range' => '20~30%'],
            'carb' => ['value' => $c_pct, 'ok' => $c_pct >= 50 && $c_pct <= 65, 'range' => '50~65%'],
        ];
    }
}
