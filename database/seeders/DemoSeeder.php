<?php

namespace Database\Seeders;

use App\Models\BodyStat;
use App\Models\Client;
use App\Models\User;
use App\Services\NutritionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $nutrition = app(NutritionService::class);

        // トレーナー作成
        $trainer = User::firstOrCreate(
            ['email' => 'trainer@pgms.test'],
            [
                'name'     => 'サンプル トレーナー',
                'password' => Hash::make('password'),
                'role'     => 1,
            ]
        );

        // 顧客作成
        $client = Client::firstOrCreate(
            ['line_user_id' => 'demo_001'],
            [
                'trainer_id'    => $trainer->id,
                'name'          => '山田 花子',
                'height'        => 158.0,
                'gender'        => 2,
                'birth_date'    => '1990-05-15',
                'pal_level'     => 2,
                'target_weight' => 52.0,
                'medical_notes' => null,
                'is_active'     => true,
            ]
        );

        // 過去12週分の体重データを生成
        $startWeight = 65.0;
        $dailyDrop   = 0.07; // 約70g/日減量

        BodyStat::where('client_id', $client->id)->delete();

        for ($week = 12; $week >= 0; $week--) {
            $date   = now()->subWeeks($week)->startOfWeek();
            $weight = round($startWeight - ($dailyDrop * (12 - $week) * 7), 1);

            // 少しランダム性を加える
            $weight += round((rand(-3, 3) / 10), 1);
            $weight  = max($weight, $client->target_weight + 5);

            $bmi = $nutrition->calcBmi($weight, $client->height);

            BodyStat::create([
                'client_id'           => $client->id,
                'weight'              => $weight,
                'body_fat_percentage' => round(28.5 - ((12 - $week) * 0.2), 1),
                'muscle_mass'         => round(38.0 + ((12 - $week) * 0.05), 1),
                'bmi'                 => $bmi,
                'measured_at'         => $date->format('Y-m-d'),
            ]);
        }

        $this->command->info("デモ顧客UUID: {$client->uuid}");
        $this->command->info("公開URL: /progress/{$client->uuid}");
    }
}