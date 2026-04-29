<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Reservation;
use Carbon\Carbon;

class LineReservationService
{
    /**
     * LINEの予約テキストを解析してreservationsに保存
     * 対応パターン：
     * 「5月3日10時お願いします」
     * 「来週月曜の14時に予約したい」
     * 「明日の13時はどうでしょう」
     */
    public function handle(Client $client, string $text): ?array
    {
        $parsed = $this->parseDateTime($text);
        if (! $parsed) {
            return null;
        }

        [$startAt, $endAt] = $parsed;

        // 重複チェック
        $overlap = Reservation::where('trainer_id', $client->trainer_id)
            ->where('status', '!=', 2)
            ->where(function ($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                    ->orWhereBetween('end_at', [$startAt, $endAt]);
            })->exists();

        if ($overlap) {
            return ['conflict' => true, 'start_at' => $startAt];
        }

        // 仮予約として作成（status: 0）
        $reservation = Reservation::create([
            'client_id'  => $client->id,
            'trainer_id' => $client->trainer_id,
            'start_at'   => $startAt,
            'end_at'     => $endAt,
            'status'     => 0,
        ]);

        \Log::info('LINE reservation saved', [
            'client_id'      => $client->id,
            'reservation_id' => $reservation->id,
            'start_at'       => $startAt,
        ]);


        return [
            'conflict'    => false,
            'reservation' => $reservation,
            'start_at'    => $startAt,
            'end_at'      => $endAt,
        ];
    }

    /**
     * 日本語テキストから日時を解析
     */
    private function parseDateTime(string $text): ?array
    {
        $now = now();

        // 「明日」「あした」
        if (preg_match('/明日|あした/', $text)) {
            $base = $now->copy()->addDay();
        }
        // 「来週月曜」等
        elseif (preg_match('/来週\s*([月火水木金土日])曜?/', $text, $m)) {
            $days = ['月' => 1, '火' => 2, '水' => 3, '木' => 4, '金' => 5, '土' => 6, '日' => 0];
            $dow  = $days[$m[1]] ?? 1;
            $base = $now->copy()->addWeek()->startOfWeek()->addDays($dow - 1);
        }
        // 「今週月曜」等
        elseif (preg_match('/今週\s*([月火水木金土日])曜?/', $text, $m)) {
            $days = ['月' => 1, '火' => 2, '水' => 3, '木' => 4, '金' => 5, '土' => 6, '日' => 0];
            $dow  = $days[$m[1]] ?? 1;
            $base = $now->copy()->startOfWeek()->addDays($dow - 1);
        }
        // 「5月3日」「5/3」
        elseif (preg_match('/([0-9１-９]+)[月\/]([0-9１-９]+)[日]?/', $text, $m)) {
            $month = (int) mb_convert_kana($m[1], 'n');
            $day   = (int) mb_convert_kana($m[2], 'n');
            $year  = $now->year;
            $base  = Carbon::create($year, $month, $day);
            // 過去の日付なら翌年
            if ($base->lt($now)) $base->addYear();
        } else {
            return null;
        }

        // 時間を抽出「10時」「14:00」「午後2時」
        $hour = null;
        if (preg_match('/午後\s*([0-9１-９]+)時/', $text, $m)) {
            $hour = (int) mb_convert_kana($m[1], 'n') + 12;
        } elseif (preg_match('/午前\s*([0-9１-９]+)時/', $text, $m)) {
            $hour = (int) mb_convert_kana($m[1], 'n');
        } elseif (preg_match('/([0-9１-９]{1,2})[時:]([0-9０-９]{2})?/', $text, $m)) {
            $hour = (int) mb_convert_kana($m[1], 'n');
        }

        if ($hour === null) return null;

        $startAt = $base->copy()->setHour($hour)->setMinute(0)->setSecond(0);
        $endAt   = $startAt->copy()->addHour();

        return [$startAt, $endAt];
    }
}
