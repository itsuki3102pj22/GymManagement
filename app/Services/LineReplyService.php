<?php

namespace App\Services;

use App\Models\Client;
use GuzzleHttp\Client as HttpClient;

class LineReplyService
{
    private HttpClient $http;
    private string $token;

    public function __construct()
    {
        $this->token = config('line.channel_access_token');
        $this->http  = new HttpClient([
            'base_uri' => 'https://api.line.me',
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /**
     * 食事記録の返信
     */
    public function replyFoodLog(
        string $replyToken,
        array $result,
        Client $client
    ): void {
        $eer  = $result['eer'];
        $diff = $eer ? $result['calories'] - $eer : null;

        $diffText = '';
        if ($diff !== null) {
            $sign     = $diff >= 0 ? '+' : '';
            $diffText = "\n推奨との差：{$sign}{$diff} kcal";
        }

        $pfcText = "P：{$result['p_balance']}%　"
                 . "F：{$result['f_balance']}%　"
                 . "C：{$result['c_balance']}%";

        $message = "✅ 食事を記録しました！\n\n"
            . "📊 合計：{$result['calories']} kcal{$diffText}\n\n"
            . "🥗 PFCバランス\n{$pfcText}\n\n"
            . "📈 進捗を確認\n"
            . route('public.progress', $client->uuid);

        $this->reply($replyToken, $message);
    }

    /**
     * 仮予約作成の返信
     */
    public function replyReservation(
        string $replyToken,
        array $result
    ): void {
        if ($result['conflict']) {
            $dt      = $result['start_at']->format('m月d日 H:i');
            $message = "⚠️ {$dt} はすでに予約が入っています。\n"
                . "別の日時を教えてください。";
        } else {
            $start   = $result['start_at']->format('m月d日（D） H:i');
            $end     = $result['end_at']->format('H:i');
            $message = "📅 仮予約を受け付けました！\n\n"
                . "{$start} 〜 {$end}\n\n"
                . "トレーナーが確認後、確定のご連絡をします。\n"
                . "変更・キャンセルはトレーナーにご連絡ください。";
        }

        $this->reply($replyToken, $message);
    }

    /**
     * 予約の意図を認識できなかった場合の返信
     */
    public function replyReservationParseError(string $replyToken): void
    {
        $message = "📅 予約リクエストを受け取りました。\n\n"
            . "日時を以下の形式で送ってください：\n"
            . "例）「5月3日10時」「来週月曜の14時」「明日の13時」";

        $this->reply($replyToken, $message);
    }

    /**
     * 体重記録の返信
     */
    public function replyBodyStat(
        string $replyToken,
        float $weight,
        ?float $bmi,
        Client $client
    ): void {
        $bmiText = $bmi ? "\nBMI：{$bmi}" : '';
        $message = "⚖️ 体重を記録しました！\n\n"
            . "{$weight} kg{$bmiText}\n\n"
            . "📈 進捗グラフ\n"
            . route('public.progress', $client->uuid);

        $this->reply($replyToken, $message);
    }

    /**
     * 進捗URL送信
     */
    public function replyProgressUrl(string $replyToken, Client $client): void
    {
        $message = "📈 あなたの進捗レポートはこちらです！\n\n"
            . route('public.progress', $client->uuid);

        $this->reply($replyToken, $message);
    }

    /**
     * コマンド一覧の返信
     */
    public function replyHelp(string $replyToken): void
    {
        $message = "🤖 PGMSボットの使い方\n\n"
            . "【食事記録】\n"
            . "「朝：ごはん、卵、味噌汁」のように送ってください\n\n"
            . "【体重記録】\n"
            . "「体重 58.5」または「今日 58.5kg」と送ってください\n\n"
            . "【予約リクエスト】\n"
            . "「5月3日10時予約」のように送ってください\n\n"
            . "【進捗確認】\n"
            . "「進捗」と送ってください";

        $this->reply($replyToken, $message);
    }

    /**
     * LINE Reply API を叩く
     */
    private function reply(string $replyToken, string $text): void
    {
        try {
            $this->http->post('/v2/bot/message/reply', [
                'json' => [
                    'replyToken' => $replyToken,
                    'messages'   => [
                        ['type' => 'text', 'text' => $text],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('LINE reply error: ' . $e->getMessage());
        }
    }
}