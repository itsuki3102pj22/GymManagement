<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('clients.index') }}"
                    class="text-sm text-gray-400 hover:text-gray-600">← 顧客一覧</a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">
                    {{ $client->name }} さん
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('public.progress', $client->uuid) }}"
                    target="_blank"
                    class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    公開URL ↗
                </a>
                <a href="{{ route('workout-logs.create', $client) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                    + トレーニング記録
                </a>
                <a href="{{ route('clients.edit', $client) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    編集
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- 基本情報 + メディカルノート --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- 基本情報 --}}
                <div class="md:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                        基本情報
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400">性別</p>
                            <p class="font-medium text-gray-700">{{ $client->genderLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400">年齢</p>
                            <p class="font-medium text-gray-700">{{ $client->age }} 歳</p>
                        </div>
                        <div>
                            <p class="text-gray-400">身長</p>
                            <p class="font-medium text-gray-700">{{ $client->height }} cm</p>
                        </div>
                        <div>
                            <p class="text-gray-400">活動レベル</p>
                            <p class="font-medium text-gray-700">{{ $client->palLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400">目標体重</p>
                            <p class="font-medium text-gray-700">
                                {{ $client->target_weight ? $client->target_weight . ' kg' : '未設定' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400">推奨カロリー(EER)</p>
                            <p class="font-medium text-gray-700">
                                {{ $eer ? number_format($eer) . ' kcal' : '算出不可' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400">目標BMI体重範囲</p>
                            <p class="font-medium text-gray-700">
                                {{ $targetRange['min'] }}〜{{ $targetRange['max'] }} kg
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400">担当トレーナー</p>
                            <p class="font-medium text-gray-700">{{ $client->trainer->name }}</p>
                        </div>
                    </div>
                </div>

                {{-- メディカルノート --}}
                <div class="bg-red-50 border border-red-100 rounded-xl p-6">
                    <h3 class="font-semibold text-red-600 mb-3 text-sm">
                        ⚠️ メディカルノート
                    </h3>
                    <p class="text-sm text-red-700 leading-relaxed">
                        {{ $client->medical_notes ?? '特記事項なし' }}
                    </p>
                </div>
            </div>

            {{-- 現在の数値 --}}
            @if($latestStat)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    最新計測値
                    <span class="text-gray-400 font-normal text-xs ml-2">
                        {{ $latestStat->measured_at->format('Y/m/d') }}
                    </span>
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-400 mb-1">体重</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $latestStat->weight }}</p>
                        <p class="text-xs text-gray-400">kg</p>
                    </div>
                    @if($bmiData)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-400 mb-1">BMI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $bmiData['bmi'] }}</p>
                        <p class="text-xs {{ $bmiData['in_range'] ? 'text-green-500' : 'text-red-500' }}">
                            {{ $bmiData['label'] }}
                        </p>
                    </div>
                    @endif
                    @if($latestStat->body_fat_percentage)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-400 mb-1">体脂肪率</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $latestStat->body_fat_percentage }}</p>
                        <p class="text-xs text-gray-400">%</p>
                    </div>
                    @endif
                    @if($latestStat->muscle_mass)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-400 mb-1">筋肉量</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $latestStat->muscle_mass }}</p>
                        <p class="text-xs text-gray-400">kg</p>
                    </div>
                    @endif
                    @if($client->target_weight)
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-purple-400 mb-1">目標まで</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ round($latestStat->weight - $client->target_weight, 1) }}
                        </p>
                        <p class="text-xs text-purple-400">kg</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- 体重推移グラフ --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                        体重推移グラフ
                    </h3>
                    @if($progress['predicted_date'] && $progress['predicted_date'] !== '達成済み')
                    <span class="text-sm text-purple-600 font-medium">
                        🎯 目標達成予定：{{ $progress['predicted_date'] }}
                    </span>
                    @endif
                </div>

                {{-- 凡例 --}}
                <div class="flex gap-4 mb-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 bg-blue-500 rounded"></span>実績
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 bg-amber-400 rounded border-dashed"></span>予測
                    </span>
                    @if($client->target_weight)
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 bg-emerald-500 rounded"></span>
                        目標 {{ $client->target_weight }}kg
                    </span>
                    @endif
                </div>

                <div style="position:relative; height:300px;">
                    <canvas id="weightChart"
                        role="img"
                        aria-label="{{ $client->name }}さんの体重推移グラフ">
                    </canvas>
                </div>
            </div>

            {{-- 計測入力フォーム + 計測履歴 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- 計測入力フォーム --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                        計測データ入力
                    </h3>
                    <form action="{{ route('body-stats.store', $client) }}" method="POST"
                        class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">計測日</label>
                            <input type="date" name="measured_at"
                                value="{{ old('measured_at', today()->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('measured_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">体重 (kg) *</label>
                            <input type="number" name="weight" step="0.1" min="20" max="300"
                                value="{{ old('weight') }}" placeholder="60.0"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('weight')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">体脂肪率 (%)</label>
                            <input type="number" name="body_fat_percentage" step="0.1" min="1" max="70"
                                value="{{ old('body_fat_percentage') }}" placeholder="20.0"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">筋肉量 (kg)</label>
                            <input type="number" name="muscle_mass" step="0.1" min="10" max="150"
                                value="{{ old('muscle_mass') }}" placeholder="38.0"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm
                                       font-medium hover:bg-blue-700 transition">
                            記録する
                        </button>
                    </form>
                </div>

                {{-- 計測履歴 --}}
                <div class="md:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                        計測履歴
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-gray-400 border-b">
                                <tr>
                                    <th class="pb-2 text-left">計測日</th>
                                    <th class="pb-2 text-right">体重</th>
                                    <th class="pb-2 text-right">体脂肪率</th>
                                    <th class="pb-2 text-right">筋肉量</th>
                                    <th class="pb-2 text-right">BMI</th>
                                    <th class="pb-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($client->bodyStats->sortByDesc('measured_at') as $stat)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 text-gray-600">
                                        {{ $stat->measured_at->format('Y/m/d') }}
                                    </td>
                                    <td class="py-2 text-right font-medium text-gray-800">
                                        {{ $stat->weight }} kg
                                    </td>
                                    <td class="py-2 text-right text-gray-500">
                                        {{ $stat->body_fat_percentage
                                            ? $stat->body_fat_percentage . '%'
                                            : '—' }}
                                    </td>
                                    <td class="py-2 text-right text-gray-500">
                                        {{ $stat->muscle_mass
                                            ? $stat->muscle_mass . ' kg'
                                            : '—' }}
                                    </td>
                                    <td class="py-2 text-right text-gray-500">
                                        {{ $stat->bmi ?? '—' }}
                                    </td>
                                    <td class="py-2 text-right">
                                        <form action="{{ route('body-stats.destroy', [$client, $stat]) }}"
                                            method="POST"
                                            onsubmit="return confirm('この計測データを削除しますか？')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-600 text-xs">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6"
                                        class="py-6 text-center text-gray-400 text-sm">
                                        計測データがありません。
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 最近のトレーニング --}}
            @if($recentWorkouts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    最近のトレーニング
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-400 border-b">
                            <tr>
                                <th class="pb-2 text-left">日付</th>
                                <th class="pb-2 text-left">種目</th>
                                <th class="pb-2 text-right">重量</th>
                                <th class="pb-2 text-right">回数</th>
                                <th class="pb-2 text-right">セット</th>
                                <th class="pb-2 text-right">総負荷</th>
                                <th class="pb-2 text-center">強度</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentWorkouts as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-500">
                                    {{ $log->logged_at->format('Y/m/d') }}
                                </td>
                                <td class="py-2 font-medium text-gray-800">
                                    {{ $log->menu->name }}
                                    <span class="text-xs text-gray-400 ml-1">
                                        {{ $log->menu->category }}
                                    </span>
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->weight }} kg
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->reps }} 回
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->sets }} セット
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ number_format($log->total_volume) }} kg
                                </td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $log->intensity === 1 ? 'bg-blue-100 text-blue-600' :
                                           ($log->intensity === 2 ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-red-100 text-red-600') }}">
                                        {{ $log->intensityLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        (function() {
            const ctx = document.getElementById('weightChart');
            const actual = @json($progress['actual']);
            const forecast = @json($progress['forecast']);
        const target = {{ $client->target_weight ?? 'null' }};
            const allWeights = [
                ...actual.map(p => p.y),
                ...forecast.map(p => p.y),
                target,
            ].filter(v => v !== null);

            const yMin = allWeights.length ? Math.floor(Math.min(...allWeights) - 2) : 40;
            const yMax = allWeights.length ? Math.ceil(Math.max(...allWeights) + 2) : 100;

            new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                            label: '実績体重',
                            data: actual,
                            parsing: {
                                xAxisKey: 'x',
                                yAxisKey: 'y'
                            },
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.07)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                            borderWidth: 2.5,
                        },
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
                        }
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
                                callback: v => v + ' kg',
                            },
                        },
                    },
                },
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

</x-app-layout>