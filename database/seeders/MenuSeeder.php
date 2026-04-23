<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // 胸
            ['name' => 'ベンチプレス',         'category' => '胸'],
            ['name' => 'インクラインベンチ',    'category' => '胸'],
            ['name' => 'ダンベルフライ',        'category' => '胸'],
            ['name' => 'ケーブルクロスオーバー','category' => '胸'],
            ['name' => 'ディップス',            'category' => '胸'],
            // 背中
            ['name' => 'デッドリフト',          'category' => '背中'],
            ['name' => 'ラットプルダウン',      'category' => '背中'],
            ['name' => 'ベントオーバーロウ',    'category' => '背中'],
            ['name' => 'シーテッドロウ',        'category' => '背中'],
            ['name' => 'チンアップ',            'category' => '背中'],
            // 肩
            ['name' => 'ショルダープレス',      'category' => '肩'],
            ['name' => 'サイドレイズ',          'category' => '肩'],
            ['name' => 'フロントレイズ',        'category' => '肩'],
            ['name' => 'フェイスプル',          'category' => '肩'],
            // 脚
            ['name' => 'スクワット',            'category' => '脚'],
            ['name' => 'レッグプレス',          'category' => '脚'],
            ['name' => 'レッグカール',          'category' => '脚'],
            ['name' => 'レッグエクステンション','category' => '脚'],
            ['name' => 'ランジ',                'category' => '脚'],
            ['name' => 'カーフレイズ',          'category' => '脚'],
            // 腕
            ['name' => 'バーベルカール',        'category' => '腕'],
            ['name' => 'ダンベルカール',        'category' => '腕'],
            ['name' => 'トライセプスプッシュダウン', 'category' => '腕'],
            ['name' => 'スカルクラッシャー',    'category' => '腕'],
            // 体幹
            ['name' => 'プランク',              'category' => '体幹'],
            ['name' => 'クランチ',              'category' => '体幹'],
            ['name' => 'レッグレイズ',          'category' => '体幹'],
            // 有酸素
            ['name' => 'トレッドミル',          'category' => '有酸素'],
            ['name' => 'バイク',                'category' => '有酸素'],
            ['name' => 'ローイングマシン',      'category' => '有酸素'],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name'], 'category' => $menu['category']],
                ['is_custom' => false]
            );
        }

        $this->command->info('種目マスタを投入しました。');
    }
}