<?php

namespace Database\Seeders;

use App\Models\FoodMaster;
use Illuminate\Database\Seeder;

class FoodMasterSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // 主食
            ['food_name' => 'ごはん（茶碗1杯・150g）',   'calories' => 252, 'protein' => 3.8, 'fat' => 0.5, 'carb' => 55.7],
            ['food_name' => '食パン（6枚切り1枚）',       'calories' => 158, 'protein' => 5.6, 'fat' => 2.5, 'carb' => 28.0],
            ['food_name' => 'うどん（1玉・200g）',        'calories' => 210, 'protein' => 5.2, 'fat' => 0.8, 'carb' => 43.2],
            ['food_name' => 'そば（1玉・200g）',          'calories' => 264, 'protein' => 9.6, 'fat' => 1.6, 'carb' => 51.2],
            ['food_name' => 'パスタ（乾100g）',           'calories' => 378, 'protein' => 13.0,'fat' => 1.9, 'carb' => 71.2],
            ['food_name' => 'オートミール（40g）',        'calories' => 152, 'protein' => 5.2, 'fat' => 2.8, 'carb' => 24.6],
            // 肉類
            ['food_name' => '鶏むね肉（100g・皮なし）',  'calories' => 116, 'protein' => 24.4,'fat' => 1.9, 'carb' => 0.0],
            ['food_name' => '鶏もも肉（100g・皮なし）',  'calories' => 153, 'protein' => 22.0,'fat' => 5.0, 'carb' => 0.0],
            ['food_name' => '豚ロース（100g）',           'calories' => 263, 'protein' => 19.3,'fat' => 19.2,'carb' => 0.2],
            ['food_name' => '牛もも肉（100g）',           'calories' => 176, 'protein' => 21.3,'fat' => 10.7,'carb' => 0.5],
            ['food_name' => 'ツナ缶（1缶・70g）',        'calories' =>  70, 'protein' => 11.2,'fat' => 2.8, 'carb' => 0.1],
            // 魚介
            ['food_name' => 'さけ（100g）',               'calories' => 133, 'protein' => 22.3,'fat' => 4.1, 'carb' => 0.1],
            ['food_name' => 'まぐろ赤身（100g）',         'calories' => 125, 'protein' => 26.4,'fat' => 1.4, 'carb' => 0.1],
            ['food_name' => 'さば（100g）',               'calories' => 202, 'protein' => 20.6,'fat' => 12.1,'carb' => 0.3],
            ['food_name' => 'えび（100g）',               'calories' =>  91, 'protein' => 21.7,'fat' => 0.6, 'carb' => 0.0],
            // 卵・乳製品
            ['food_name' => '卵（1個・60g）',             'calories' =>  91, 'protein' => 7.4, 'fat' => 6.2, 'carb' => 0.2],
            ['food_name' => 'ギリシャヨーグルト（100g）', 'calories' =>  59, 'protein' => 10.0,'fat' => 0.2, 'carb' => 3.6],
            ['food_name' => '牛乳（200ml）',              'calories' => 134, 'protein' => 6.6, 'fat' => 7.6, 'carb' => 9.6],
            ['food_name' => 'プロテインシェイク（1杯）', 'calories' => 120, 'protein' => 24.0,'fat' => 1.5, 'carb' => 5.0],
            // 豆・大豆
            ['food_name' => '豆腐（1丁・300g）',         'calories' => 168, 'protein' => 16.8,'fat' => 9.9, 'carb' => 5.1],
            ['food_name' => '納豆（1パック・45g）',       'calories' =>  90, 'protein' => 7.4, 'fat' => 4.5, 'carb' => 5.4],
            ['food_name' => '枝豆（100g）',               'calories' => 135, 'protein' => 11.5,'fat' => 6.2, 'carb' => 8.8],
            // 野菜
            ['food_name' => 'ブロッコリー（100g）',       'calories' =>  37, 'protein' => 4.3, 'fat' => 0.5, 'carb' => 5.2],
            ['food_name' => 'ほうれん草（100g）',         'calories' =>  20, 'protein' => 2.2, 'fat' => 0.4, 'carb' => 3.1],
            ['food_name' => 'トマト（1個・150g）',        'calories' =>  29, 'protein' => 1.1, 'fat' => 0.2, 'carb' => 5.6],
            ['food_name' => 'アボカド（1個・150g）',      'calories' => 231, 'protein' => 3.0, 'fat' => 22.5,'carb' => 8.6],
            // 果物
            ['food_name' => 'バナナ（1本・100g）',        'calories' =>  86, 'protein' => 1.1, 'fat' => 0.2, 'carb' => 22.5],
            ['food_name' => 'りんご（1個・250g）',        'calories' => 145, 'protein' => 0.5, 'fat' => 0.5, 'carb' => 39.8],
            // その他
            ['food_name' => 'アーモンド（30g）',          'calories' => 184, 'protein' => 6.1, 'fat' => 15.9,'carb' => 5.8],
            ['food_name' => 'オリーブオイル（大さじ1）',  'calories' => 111, 'protein' => 0.0, 'fat' => 12.0,'carb' => 0.0],
        ];

        foreach ($foods as $food) {
            FoodMaster::firstOrCreate(
                ['food_name' => $food['food_name']],
                $food
            );
        }

        $this->command->info('食品マスタを投入しました（' . count($foods) . '件）');
    }
}