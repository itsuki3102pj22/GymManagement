<?php

namespace App\Http\Controllers;

use App\Models\BodyStat;
use App\Models\Client;
use App\Services\BodyStatsService;
use App\Services\LineFoodLogService;
use App\Services\LineReplyService;
use App\Services\LineReservationService;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LineWebhookController extends Controller
{
    public function __construct(
        private LineFoodLogService    $foodLog,
        private LineReservationService $reservation,
        private LineReplyService       $reply,
        private NutritionService       $nutrition,
    ) {}

    public function handle(Request $request): Response
    {
        // 受信確認ログ
        \Log::info('LINE Webhook received', [
            'body'      => $request->getContent(),
            'signature' => $request->header('X-Line-Signature'),
        ]);

        // 署名検証
        if (! $this->verifySignature($request)) {
            \Log::error('LINE Webhook signature verification failed');
            return response('Unauthorized', 401);
        }

        \Log::info('LINE Webhook signature OK');

        $events = $request->input('events', []);

        foreach ($events as $event) {
            \Log::info('LINE event', $event);

            if ($event['type'] !== 'message') continue;
            if ($event['message']['type'] !== 'text') continue;

            $lineUserId = $event['source']['userId'];
            $text       = trim($event['message']['text']);
            $replyToken = $event['replyToken'];

            \Log::info('LINE message', [
                'lineUserId' => $lineUserId,
                'text'       => $text,
            ]);

            $client = Client::where('line_user_id', $lineUserId)
                ->where('is_active', true)
                ->first();

            if (! $client) {
                \Log::warning('LINE client not found', ['lineUserId' => $lineUserId]);
                continue;
            }

            \Log::info('LINE client found', ['client_id' => $client->id, 'name' => $client->name]);

            $this->dispatch($client, $text, $replyToken);
        }

        return response('OK', 200);
    }
    /**
     * メッセージ種別を判定してServiceに振り分け
     */
    private function dispatch(
        Client $client,
        string $text,
        string $replyToken
    ): void {
        try {
            // ヘルプ
            if (preg_match('/ヘルプ|help|使い方/iu', $text)) {
                $this->reply->replyHelp($replyToken);
                return;
            }

            // 進捗URL
            if (preg_match('/進捗|グラフ|URL/iu', $text)) {
                $this->reply->replyProgressUrl($replyToken, $client);
                return;
            }

            // 体重報告
            if (preg_match(
                '/(?:体重|今日|今朝)\s*([0-9０-９]+\.?[0-9０-９]*)\s*(?:kg|キロ|ｋｇ)?/u',
                $text,
                $m
            )) {
                $weight = (float) mb_convert_kana($m[1], 'n');

                // 先に保存
                $this->handleBodyStat($client, $weight, $replyToken);
                return;
            }

            // 予約リクエスト
            if (preg_match('/予約|お願い|取りたい|入れて/u', $text)) {
                // 先に保存
                $result = $this->reservation->handle($client, $text);

                // 保存後に返信
                if ($result) {
                    $this->reply->replyReservation($replyToken, $result);
                } else {
                    $this->reply->replyReservationParseError($replyToken);
                }
                return;
            }

            // 食事報告（上記以外）
            // 先に保存
            $result = $this->foodLog->handle($client, $text);

            // 保存後に返信
            $this->reply->replyFoodLog($replyToken, $result, $client);
        } catch (\Exception $e) {
            \Log::error('LINE dispatch error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }

    private function handleBodyStat(
        Client $client,
        float $weight,
        string $replyToken
    ): void {
        $bmi = $this->nutrition->calcBmi($weight, $client->height);

        // 先に保存
        \App\Models\BodyStat::create([
            'client_id'   => $client->id,
            'weight'      => $weight,
            'bmi'         => $bmi,
            'measured_at' => today(),
        ]);

        \Log::info('LINE body stat saved', [
            'client_id' => $client->id,
            'weight'    => $weight,
            'bmi'       => $bmi,
        ]);

        // 後から返信（失敗してもデータは保存済み）
        try {
            $this->reply->replyBodyStat($replyToken, $weight, $bmi, $client);
        } catch (\Exception $e) {
            \Log::error('LINE reply failed (data already saved)', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * HMAC-SHA256で署名検証
     */
    private function verifySignature(Request $request): bool
    {
        $secret    = config('line.channel_secret');
        $signature = $request->header('X-Line-Signature');

        if (! $signature || ! $secret) {
            \Log::error('LINE verify: secret or signature missing', [
                'has_secret'    => ! empty($secret),
                'has_signature' => ! empty($signature),
            ]);
            return false;
        }

        // Laravelはbodyを既にパースしている場合があるため
        // php://input から生のボディを取得する
        $body = file_get_contents('php://input');

        // php://input が空の場合はgetContent()にフォールバック
        if (empty($body)) {
            $body = $request->getContent();
        }

        \Log::info('LINE verify debug', [
            'body_length' => strlen($body),
            'secret_length' => strlen($secret),
            'signature' => $signature,
        ]);

        $hash     = hash_hmac('sha256', $body, $secret, true);
        $expected = base64_encode($hash);

        \Log::info('LINE verify result', [
            'expected'  => $expected,
            'received'  => $signature,
            'match'     => hash_equals($expected, $signature),
        ]);

        return hash_equals($expected, $signature);
    }
}
