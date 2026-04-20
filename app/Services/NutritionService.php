<?php

namespace App\Services;

use App\Models\Client;

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

    /**
     * BMI算出
     * 体重(kg) ÷ 身長(m)²
     */
    public function calcBmi(float $weight, float $height_cm): float
    {
        $h = $height_cm / 100;
        return round($weight / ($h * $h), 1);
    }

    /**
     * BMI評価（18〜49歳は18.5〜24.9が目標範囲）
     */
    public function bmiStatus(float $bmi, int $age): array
    {
        $targetMin = 18.5;
        $targetMax = $age >= 65 ? 27.9 : 24.9; // 65歳以上は目標範囲が異なる

        $label = match (true) {
            $bmi < 18.5 => '低体重',
            $bmi <= $targetMax => '標準（目標範囲）',
            $bmi <= 29.9 => '過体重',
            default => '肥満',
        };

        return [
            'bmi' => $bmi,
            'label' => $label,
            'in_range' => $bmi >= $targetMin && $bmi <= $targetMax,
            'target_min' => $targetMin,
            'target_max' => $targetMax,
        ];
    }

    /**
     * 推定エネルギー必要量（EER）算出
     */
    public function calcEer(Client $client): ?int
    {
        $age = $client->age;
        $gender = $client->gender === 1 ? 'meal' : 'female'; // 性別判定
        $pal = $client->pal_level - 1;

        // 年齢区分の判定
        $ageKey = match (true) {
            $age < 30 => '18-29',
            $age < 50 => '30-49',
            $age < 65 => '50-64',
            $age < 75 => '65-74',
            default => '75+',
        };

        if ($ageKey === null) {
            return null;
        }

        return self::EER_TABLE[$gender][$ageKey][$pal] ?? null;
    }

    /**
     * PFCバランス評価
     * 「日本人の食事摂取基準（2025年版）」目標量
     * たんぱく質: 13〜20%E、脂質: 20〜30%E、炭水化物: 50〜65%E
     */
    public function pfcStatus(float $p_pct, float $f_pct, float $c_pct): array
    {
        return [
            'protein' => [
                'value' => $p_pct,
                'ok' => $p_pct >= 13.0 && $p_pct <= 20.0,
                'range' => '13~20%',
                'label' => 'タンパク質',
            ],
            'fat' => [
                'value' => $f_pct,
                'ok' => $f_pct >= 20.0 && $f_pct <= 30.0,
                'range' => '20~30%',
                'label' => '脂質',
            ],
            'carb' => [
                'value' => $c_pct,
                'ok' => $c_pct >= 50.0 && $c_pct <= 65.0,
                'range' => '50~65%',
                'label' => '炭水化物',
            ],
        ];
    }

    /**
     * 摂取カロリーとEERの差から過不足を評価
     */
    public function calorieBalance(int $intake, ?int $eer): array
    {
        if ($eer === null) {
            return ['diff' => null, 'label' => '算出不可', 'ok' => false];
        }

        $diff = $intake - $eer;

        $label = match (true) {
            $diff > 200 => '過剰摂取',
            $diff < -200 => '不足',
            default => '適正範囲',
        };

        return [
            'diff' => $diff,
            'label' => $label,
            'ok' => abs($diff) <= 200,
            'intake' => $intake,
            'eer' => $eer,
        ];
    }

    /**
     * 目標BMIに対応する体重範囲を算出
     */
    public function targetWeightRange(float $height_cm, int $age): array
    {
        $h   = $height_cm / 100;
        $max = $age >= 65 ? 27.9 : 24.9;

        return [
            'min' => round(18.5 * $h * $h, 1),
            'max' => round($max  * $h * $h, 1),
        ];
    }
}
