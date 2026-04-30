<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $client->name }} さんの進捗レポート</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <style>
        body {
            background: #f8fafc;
            font-family: 'Helvetica Neue', sans-serif;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }

        .stat-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .stat-box .label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 4px;
        }

        .stat-box .value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .stat-box .unit {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-ok {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-ng {
            background: #fee2e2;
            color: #b91c1c;
        }

        .pfc-bar-wrap {
            margin-bottom: 0.75rem;
        }

        .pfc-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            margin-bottom: 3px;
            color: #475569;
        }

        .pfc-bar-bg {
            background: #e2e8f0;
            border-radius: 99px;
            height: 10px;
            overflow: hidden;
        }

        .pfc-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.6s ease;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .predicted-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .predicted-banner .icon {
            font-size: 2rem;
        }

        .predicted-banner .date {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .predicted-banner .sub {
            font-size: 0.8rem;
            opacity: 0.85;
        }
    </style>
</head>

<body>

    <div style="max-width: 720px; margin: 0 auto; padding: 1.5rem 1rem;">

        {{-- ヘッダー --}}
        <div style="text-align:center; margin-bottom: 1.5rem;">
            <p style="font-size:0.85rem; color:#94a3b8; margin-bottom:4px;">進捗レポート</p>
            <h1 style="font-size:1.6rem; font-weight:700; color:#1e293b;">
                {{ $client->name }} さん
            </h1>
            @if($progress['latest_measured'])
            <p style="font-size:0.8rem; color:#94a3b8;">
                最終計測：{{ $progress['latest_measured'] }}
            </p>
            @endif
        </div>

        {{-- 目標達成予定バナー --}}
        @if($progress['predicted_date'] && $progress['predicted_date'] !== '達成済み')
        <div class="predicted-banner">
            <div class="icon">🎯</div>
            <div>
                <div class="sub">このペースで続けると...</div>
                <div class="date">{{ $progress['predicted_date'] }} に目標達成！</div>
                @if($progress['daily_rate_g'])
                <div class="sub">現在の減量ペース：約 {{ $progress['daily_rate_g'] }}g / 日</div>
                @endif
            </div>
        </div>
        @elseif($progress['predicted_date'] === '達成済み')
        <div class="predicted-banner" style="background: linear-gradient(135deg,#11998e,#38ef7d);">
            <div class="icon">🏆</div>
            <div>
                <div class="date">目標体重を達成しました！</div>
                <div class="sub">素晴らしい成果です。この調子を維持しましょう。</div>
            </div>
        </div>
        @endif

        {{-- 現在の数値 --}}
        @if($latestStat)
        <div class="card">
            <div class="section-title">現在の身体データ</div>
            <div class="stat-grid">
                <div class="stat-box">
                    <div class="label">体重</div>
                    <div class="value">{{ $latestStat->weight }}</div>
                    <div class="unit">kg</div>
                </div>
                @if($bmiData)
                <div class="stat-box">
                    <div class="label">BMI</div>
                    <div class="value" style="font-size:1.2rem;">
                        {{ $bmiData['bmi'] }}
                    </div>
                    <div class="unit" style="margin-top:4px;">
                        <span class="badge {{ $bmiData['in_range'] ? 'badge-ok' : 'badge-ng' }}">
                            {{ $bmiData['label'] }}
                        </span>
                    </div>
                </div>
                @endif
                @if($latestStat->body_fat_percentage)
                <div class="stat-box">
                    <div class="label">体脂肪率</div>
                    <div class="value">{{ $latestStat->body_fat_percentage }}</div>
                    <div class="unit">%</div>
                </div>
                @endif
                @if($latestStat->muscle_mass)
                <div class="stat-box">
                    <div class="label">筋肉量</div>
                    <div class="value">{{ $latestStat->muscle_mass }}</div>
                    <div class="unit">kg</div>
                </div>
                @endif
                @if($client->target_weight)
                <div class="stat-box">
                    <div class="label">目標まで</div>
                    <div class="value" style="color:#7c3aed;">
                        {{ round($latestStat->weight - $client->target_weight, 1) }}
                    </div>
                    <div class="unit">kg</div>
                </div>
                @endif
                @if($eer)
                <div class="stat-box">
                    <div class="label">推奨摂取カロリー</div>
                    <div class="value">{{ number_format($eer) }}</div>
                    <div class="unit">kcal / 日</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- 体重推移グラフ --}}
        <div class="card">
            <div class="section-title">体重推移グラフ</div>

            {{-- 凡例 --}}
            <div style="display:flex; gap:1.5rem; margin-bottom:1rem; font-size:0.8rem; color:#475569;">
                <span style="display:flex;align-items:center;gap:5px;">
                    <span style="display:inline-block;width:24px;height:3px;background:#3b82f6;border-radius:2px;"></span>
                    実績体重
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <span style="display:inline-block;width:24px;height:3px;background:#f59e0b;border-radius:2px;border-top:2px dashed #f59e0b;"></span>
                    予測体重
                </span>
                @if($client->target_weight)
                <span style="display:flex;align-items:center;gap:5px;">
                    <span style="display:inline-block;width:24px;height:2px;background:#10b981;"></span>
                    目標 {{ $client->target_weight }}kg
                </span>
                @endif
            </div>

            <div style="position:relative; height:320px;">
                <canvas id="weightChart"
                    role="img"
                    aria-label="{{ $client->name }}さんの体重推移グラフ。実績と予測を表示。">
                </canvas>
            </div>
        </div>

        {{-- PFCバランス --}}
        @if($pfcStatus)
        <div class="card">
            <div class="section-title">最新のPFCバランス</div>
            @foreach($pfcStatus as $key => $item)
            <div class="pfc-bar-wrap">
                <div class="pfc-label-row">
                    <span>
                        {{ $item['label'] }}
                        <span class="badge {{ $item['ok'] ? 'badge-ok' : 'badge-ng' }}">
                            {{ $item['ok'] ? '目標範囲内' : '要調整' }}
                        </span>
                    </span>
                    <span>{{ $item['value'] }}% <span style="color:#94a3b8;">（目標 {{ $item['range'] }}）</span></span>
                </div>
                <div class="pfc-bar-bg">
                    <div class="pfc-bar-fill" style="
                    width: {{ min($item['value'], 100) }}%;
                    background: {{ $item['ok'] ? '#22c55e' : '#ef4444' }};
                "></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- フッター --}}
        <div style="text-align:center; font-size:0.75rem; color:#cbd5e1; margin-top:2rem; padding-bottom:2rem;">
            このページはあなた専用の進捗レポートです。<br>
            厚生労働省「日本人の食事摂取基準（2025年版）」準拠
        </div>

    </div>

    {{-- Chart.js グラフ描画 --}}
    <script>
        (function() {
            const actual = @json($progress['actual']);
            const forecast = @json($progress['forecast']);
            const target = @json($client->target_weight);

            const ctx = document.getElementById('weightChart');
            if (!ctx) return;

            // Y軸の範囲を自動調整
            const allWeights = [
                ...actual.map(p => p.y),
                ...forecast.map(p => p.y),
                target,
            ].filter(v => v !== null);

            const yMin = allWeights.length ?
                Math.floor(Math.min(...allWeights) - 2) :
                40;
            const yMax = allWeights.length ?
                Math.ceil(Math.max(...allWeights) + 2) :
                100;

            new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [
                        // 実績（実線）
                        {
                            label: '実績体重',
                            data: actual,
                            parsing: {
                                xAxisKey: 'x',
                                yAxisKey: 'y'
                            },
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.08)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                            borderWidth: 2.5,
                        },
                        // 予測（点線）
                        {
                            label: '予測体重',
                            data: forecast,
                            parsing: {
                                xAxisKey: 'x',
                                yAxisKey: 'y'
                            },
                            borderColor: '#f59e0b',
                            backgroundColor: 'transparent',
                            borderDash: [6, 4],
                            tension: 0.35,
                            pointRadius: 2,
                            pointBackgroundColor: '#f59e0b',
                            borderWidth: 2,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y} kg`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'week',
                                displayFormats: {
                                    week: 'M/d'
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.04)'
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            },
                        },
                        y: {
                            min: yMin,
                            max: yMax,
                            grid: {
                                color: 'rgba(0,0,0,0.04)'
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                callback: (v) => v + ' kg',
                            },
                        },
                    },
                },
                // 目標体重ラインをプラグインで描画
                plugins: [{
                    id: 'targetLine',
                    afterDraw(chart) {
                        if (target === null) return;
                        const {
                            ctx,
                            scales: {
                                x,
                                y
                            }
                        } = chart;
                        const yPos = y.getPixelForValue(target);
                        ctx.save();
                        ctx.strokeStyle = '#10b981';
                        ctx.lineWidth = 1.5;
                        ctx.setLineDash([4, 4]);
                        ctx.beginPath();
                        ctx.moveTo(x.left, yPos);
                        ctx.lineTo(x.right, yPos);
                        ctx.stroke();
                        ctx.fillStyle = '#10b981';
                        ctx.font = '11px sans-serif';
                        ctx.fillText(`目標 ${target}kg`, x.right - 72, yPos - 5);
                        ctx.restore();
                    },
                }],
            });
        })();
    </script>

</body>

</html>