<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('clients.index') }}"
                    class="text-sm transition"
                    style="color:var(--text-muted)">← 顧客一覧</a>
                <h2 class="font-display mt-1"
                    style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                    {{ $client->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('public.progress', $client->uuid) }}" target="_blank"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    style="border:1px solid var(--blue-border);color:var(--royal);background:var(--blue-bg)">
                    公開URL ↗
                </a>
                <a href="{{ route('workout-logs.create', $client) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white transition"
                    style="background:var(--royal)">
                    + トレーニング記録
                </a>
                <a href="{{ route('food-logs.index', $client) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    style="border:1px solid var(--card-border);color:var(--text-secondary)">
                    食事ログ
                </a>
                <a href="{{ route('clients.edit', $client) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    style="border:1px solid var(--gold-light);color:var(--gold);background:var(--gold-bg)">
                    編集
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- 目標達成予測バナー --}}
            @if($progress['predicted_date'])
            <div class="rounded-2xl p-5 flex items-center gap-5 relative overflow-hidden"
                style="background:linear-gradient(135deg,var(--navy) 0%,var(--royal) 60%,var(--royal-light) 100%)">
                <div class="absolute right-0 top-0 w-40 h-40 rounded-full opacity-5"
                    style="background:#fff;transform:translate(20px,-20px)"></div>
                <div class="text-3xl relative z-10">🎯</div>
                <div class="relative z-10">
                    <p class="text-xs tracking-widest uppercase mb-1" style="color:#93b8e0">
                        Goal Prediction
                    </p>
                    <p class="font-display text-xl text-white" style="font-weight:400">
                        {{ $progress['predicted_date'] === '達成済み'
                            ? '🏆 目標体重を達成しました！'
                            : $progress['predicted_date'] . ' に目標達成予定' }}
                    </p>
                    @if($progress['daily_rate_g'])
                    <p class="text-xs mt-1" style="color:#93b8e0">
                        減量ペース：約 {{ $progress['daily_rate_g'] }}g ／ 日　　
                        記録数：{{ $progress['total_records'] }} 回
                    </p>
                    @endif
                </div>
                @if($client->target_weight && $latestStat)
                <div class="relative z-10 ml-auto text-right">
                    <p class="text-3xl font-semibold text-white leading-none">
                        {{ round($latestStat->weight - $client->target_weight, 1) }}
                        <span class="text-base font-light" style="color:#93b8e0">kg</span>
                    </p>
                    <p class="text-xs mt-1" style="color:#93b8e0">目標まで</p>
                    <div class="h-0.5 rounded-full mt-2 ml-auto w-10"
                        style="background:var(--gold)"></div>
                </div>
                @endif
            </div>
            @endif

            {{-- 基本情報 + 最新計測値 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- 基本情報 --}}
                <div class="rounded-2xl p-5 bg-white"
                    style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                    <p class="text-xs font-semibold tracking-widest uppercase mb-4"
                        style="color:var(--gold)">基本情報</p>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center
                                    font-display text-xl flex-shrink-0"
                            style="background:var(--blue-bg);border:2px solid var(--gold-light);
                                    color:var(--royal);font-weight:400">
                            {{ mb_substr($client->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold" style="color:var(--navy);font-size:16px">
                                {{ $client->name }}
                            </p>
                            <p class="text-xs mt-0.5" style="color:var(--text-muted)">
                                {{ $client->birth_date->format('Y.m.d') }} 生
                                &nbsp;|&nbsp; {{ $client->age }} 歳
                                &nbsp;|&nbsp; {{ $client->genderLabel() }}
                            </p>
                            <div class="flex gap-1 mt-1.5">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                    style="background:var(--blue-bg);color:var(--royal);
                                             border:1px solid var(--blue-border)">
                                    {{ $client->palLabel() }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                    style="background:var(--green-bg);color:var(--green-dark);
                                             border:1px solid var(--green-border)">
                                    契約中
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach([
                        ['label' => '身長', 'value' => $client->height . ' cm'],
                        ['label' => '目標体重', 'value' => ($client->target_weight ? $client->target_weight . ' kg' : '未設定'), 'gold' => true],
                        ['label' => '推奨 EER', 'value' => ($eer ? number_format($eer) . ' kcal' : '算出不可')],
                        ['label' => '担当', 'value' => $client->trainer->name],
                        ['label' => 'BMI目標範囲','value' => $targetRange['min'] . '〜' . $targetRange['max'] . ' kg'],
                        ['label' => '来店回数', 'value' => $client->workoutLogs->count() . ' 回'],
                        ] as $row)
                        <div class="flex justify-between items-center px-3 py-2 rounded-lg"
                            style="background:var(--surface)">
                            <span style="color:var(--text-muted)">{{ $row['label'] }}</span>
                            <span class="font-medium"
                                style="color:{{ ($row['gold'] ?? false) ? 'var(--gold)' : 'var(--navy)' }}">
                                {{ $row['value'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @if($client->medical_notes)
                    <div class="mt-3 rounded-lg p-3"
                        style="background:#fff8f0;border:1px solid #f0d9b8;border-left:3px solid var(--gold)">
                        <p class="text-xs font-semibold tracking-wider mb-1"
                            style="color:var(--gold)">⚠ Medical Note</p>
                        <p class="text-xs" style="color:#8a7a5a">{{ $client->medical_notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- 最新計測値 --}}
                <div class="rounded-2xl p-5 bg-white"
                    style="border:1px solid var(--card-border)">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-xs font-semibold tracking-widest uppercase"
                            style="color:var(--gold)">最新計測値</p>
                        @if($latestStat)
                        <span class="text-xs" style="color:var(--text-muted)">
                            {{ $latestStat->measured_at->format('Y.m.d') }}
                        </span>
                        @endif
                    </div>

                    @if($latestStat)
                    <p class="text-xs mb-3" style="color:var(--text-muted)">
                        数値をクリックすると計測入力フォームが開きます
                    </p>
                    @endif

                    {{-- 計測値カード --}}
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach([
                        ['label' => '体重', 'value' => $latestStat?->weight, 'unit' => 'kg', 'color' => 'var(--navy)', 'key' => 'weight'],
                        ['label' => 'BMI', 'value' => $bmiData['bmi'] ?? null, 'unit' => '', 'color' => 'var(--royal)', 'key' => 'bmi'],
                        ['label' => '体脂肪率', 'value' => $latestStat?->body_fat_percentage, 'unit' => '%', 'color' => 'var(--gold)', 'key' => 'fat'],
                        ['label' => '筋肉量', 'value' => $latestStat?->muscle_mass, 'unit' => 'kg', 'color' => 'var(--green-dark)', 'key' => 'muscle'],
                        ] as $stat)
                        <div class="rounded-xl p-3 text-center cursor-pointer transition-all duration-150 stat-tap-card"
                            data-key="{{ $stat['key'] }}"
                            style="background:var(--surface);border:1px solid var(--card-border)">
                            <p class="text-2xl font-semibold leading-none"
                                style="color:{{ $stat['color'] }}">
                                {{ $stat['value'] ?? '—' }}
                            </p>
                            <p class="text-xs mt-1" style="color:var(--text-muted)">
                                {{ $stat['label'] }}
                                @if($stat['unit']) {{ $stat['unit'] }} @endif
                            </p>
                            @if($stat['key'] === 'bmi' && $bmiData)
                            <p class="text-xs mt-1"
                                style="color:{{ $bmiData['in_range'] ? 'var(--green-dark)' : '#dc2626' }}">
                                {{ $bmiData['label'] }}
                            </p>
                            @endif
                            <p class="text-xs mt-1" style="color:var(--gold)">タップで入力</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- 計測入力フォーム（タップで展開） --}}
                    <div id="measureForm" class="hidden rounded-xl p-4"
                        style="background:var(--blue-bg);border:1px solid var(--blue-border)">
                        <p class="text-xs font-semibold mb-3" style="color:var(--royal)">
                            計測データを入力
                        </p>
                        <form action="{{ route('body-stats.store', $client) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label class="block text-xs mb-1" style="color:var(--text-muted)">
                                        計測日 *
                                    </label>
                                    <input type="date" name="measured_at"
                                        value="{{ today()->format('Y-m-d') }}"
                                        class="w-full rounded-lg px-3 py-1.5 text-sm"
                                        style="border:1px solid var(--blue-border);
                                                  background:#fff;color:var(--navy)">
                                </div>
                                <div>
                                    <label class="block text-xs mb-1" style="color:var(--text-muted)">
                                        体重 (kg) *
                                    </label>
                                    <input type="number" name="weight" id="prefill-weight"
                                        step="0.1" min="20" max="300"
                                        placeholder="{{ $latestStat?->weight ?? '60.0' }}"
                                        class="w-full rounded-lg px-3 py-1.5 text-sm"
                                        style="border:1px solid var(--blue-border);
                                                  background:#fff;color:var(--navy)">
                                </div>
                                <div>
                                    <label class="block text-xs mb-1" style="color:var(--text-muted)">
                                        体脂肪率 (%)
                                    </label>
                                    <input type="number" name="body_fat_percentage"
                                        id="prefill-fat"
                                        step="0.1" min="1" max="70"
                                        placeholder="{{ $latestStat?->body_fat_percentage ?? '20.0' }}"
                                        class="w-full rounded-lg px-3 py-1.5 text-sm"
                                        style="border:1px solid var(--blue-border);
                                                  background:#fff;color:var(--navy)">
                                </div>
                                <div>
                                    <label class="block text-xs mb-1" style="color:var(--text-muted)">
                                        筋肉量 (kg)
                                    </label>
                                    <input type="number" name="muscle_mass"
                                        id="prefill-muscle"
                                        step="0.1" min="10" max="150"
                                        placeholder="{{ $latestStat?->muscle_mass ?? '38.0' }}"
                                        class="w-full rounded-lg px-3 py-1.5 text-sm"
                                        style="border:1px solid var(--blue-border);
                                                  background:#fff;color:var(--navy)">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="closeMeasureForm"
                                    class="px-4 py-1.5 rounded-lg text-xs"
                                    style="border:1px solid var(--blue-border);
                                               color:var(--text-secondary)">
                                    キャンセル
                                </button>
                                <button type="submit"
                                    class="px-5 py-1.5 rounded-lg text-xs font-semibold text-white"
                                    style="background:var(--royal)">
                                    記録する
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- PFCバランス --}}
                    @if($pfcStatus)
                    <div class="rounded-xl p-3 mt-2" style="background:var(--surface)">
                        <p class="text-xs font-semibold tracking-widest uppercase mb-2"
                            style="color:var(--gold)">PFC バランス</p>
                        @foreach($pfcStatus as $key => $item)
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-bold w-3 text-center"
                                style="color:{{ $key === 'protein' ? 'var(--royal)' :
                                                 ($key === 'fat' ? 'var(--gold)' : 'var(--green-dark)') }}">
                                {{ strtoupper(substr($key, 0, 1)) }}
                            </span>
                            <div class="flex-1 rounded-full h-1.5 overflow-hidden"
                                style="background:var(--card-border)">
                                <div class="h-full rounded-full"
                                    style="width:{{ min($item['value'], 100) }}%;
                                            background:{{ $key === 'protein' ? 'var(--royal)' :
                                                         ($key === 'fat' ? 'var(--gold)' : 'var(--green-dark)') }}">
                                </div>
                            </div>
                            <span class="text-xs w-8 text-right" style="color:var(--text-muted)">
                                {{ $item['value'] }}%
                            </span>
                            <span class="text-xs px-1.5 py-0.5 rounded-full"
                                style="background:{{ $item['ok'] ? 'var(--green-bg)' : '#fef2f2' }};
                                         color:{{ $item['ok'] ? 'var(--green-dark)' : '#dc2626' }}">
                                {{ $item['ok'] ? '◎' : '△' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- カレンダー --}}
            @php
            $calendarYear = request('cal_year', now()->year);
            $calendarMonth = request('cal_month', now()->month);
            $calStart = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1);
            $calEnd = $calStart->copy()->endOfMonth();
            $firstDow = ($calStart->dayOfWeek + 6) % 7; // 月曜始まり

            // 計測・トレーニング・予約データを日付でグループ化
            $bodyStatsByDate = $client->bodyStats
            ->whereBetween('measured_at', [$calStart, $calEnd])
            ->groupBy(fn($s) => \Carbon\Carbon::parse($s->measured_at)->format('Y-m-d'));

            $workoutsByDate = $client->workoutLogs
            ->whereBetween('logged_at', [$calStart, $calEnd])
            ->groupBy(fn($w) => \Carbon\Carbon::parse($w->logged_at)->format('Y-m-d'));

            $reservationsByDate = $client->reservations
            ->whereBetween('start_at', [$calStart, $calEnd])
            ->groupBy(fn($r) => \Carbon\Carbon::parse($r->start_at)->format('Y-m-d'));

            $prevMonth = $calStart->copy()->subMonth();
            $nextMonth = $calStart->copy()->addMonth();
            @endphp

            <div class="rounded-2xl p-5 bg-white" style="border:1px solid var(--card-border)">
                {{-- カレンダーヘッダー --}}
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold tracking-widest uppercase"
                        style="color:var(--gold)">
                        カレンダー — {{ $calStart->format('Y年 n月') }}
                    </p>
                    <div class="flex items-center gap-3">
                        {{-- 凡例 --}}
                        <div class="flex gap-3 text-xs" style="color:var(--text-muted)">
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full inline-block"
                                    style="background:var(--royal)"></span>
                                トレーニング
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full inline-block"
                                    style="background:var(--gold)"></span>
                                計測
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full inline-block"
                                    style="background:var(--green)"></span>
                                予約（確定）
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full inline-block"
                                    style="background:var(--amber)"></span>
                                予約（仮）
                            </span>
                        </div>
                        {{-- 月移動 --}}
                        <div class="flex gap-1">
                            <a href="{{ route('clients.show', $client) . '?cal_year=' . $prevMonth->year . '&cal_month=' . $prevMonth->month }}"
                                class="text-xs px-3 py-1 rounded-lg transition"
                                style="border:1px solid var(--blue-border);color:var(--royal);background:var(--blue-bg)">
                                ← 前月
                            </a>
                            <a href="{{ route('clients.show', $client) . '?cal_year=' . $nextMonth->year . '&cal_month=' . $nextMonth->month }}"
                                class="text-xs px-3 py-1 rounded-lg transition"
                                style="border:1px solid var(--blue-border);color:var(--royal);background:var(--blue-bg)">
                                次月 →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 曜日ラベル --}}
                <div class="grid grid-cols-7 gap-1 mb-1">
                    @foreach(['月','火','水','木','金','土','日'] as $i => $dow)
                    <div class="text-center text-xs py-1 font-medium"
                        style="color:{{ $i === 5 ? 'var(--royal)' : ($i === 6 ? '#dc2626' : 'var(--text-muted)') }}">
                        {{ $dow }}
                    </div>
                    @endforeach
                </div>

                {{-- 日付グリッド --}}
                <div class="grid grid-cols-7 gap-1" id="calGrid">
                    {{-- 月初の空白 --}}
                    @for($i = 0; $i < $firstDow; $i++)
                        <div>
                </div>
                @endfor

                @for($day = 1; $day <= $calEnd->day; $day++)
                    @php
                    $dateStr = $calStart->copy()->day($day)->format('Y-m-d');
                    $isToday = $dateStr === now()->format('Y-m-d');
                    $hasW = isset($workoutsByDate[$dateStr]);
                    $hasM = isset($bodyStatsByDate[$dateStr]);
                    $resGroup = $reservationsByDate[$dateStr] ?? collect();
                    $hasRConf = $resGroup->where('status', 1)->count() > 0;
                    $hasRPend = $resGroup->where('status', 0)->count() > 0;
                    $hasAny = $hasW || $hasM || $hasRConf || $hasRPend;

                    $bgStyle = 'background:var(--surface);border-color:var(--card-border)';
                    if ($isToday) {
                    $bgStyle = 'background:var(--gold-bg);border-color:var(--gold);box-shadow:0 0 0 1px rgba(184,149,42,0.2)';
                    } elseif ($hasW && $hasRConf) {
                    $bgStyle = 'background:linear-gradient(145deg,var(--blue-bg),var(--green-bg));border-color:var(--blue-border)';
                    } elseif ($hasW) {
                    $bgStyle = 'background:var(--blue-bg);border-color:var(--blue-border)';
                    } elseif ($hasM) {
                    $bgStyle = 'background:var(--gold-bg);border-color:var(--gold-light)';
                    } elseif ($hasRConf) {
                    $bgStyle = 'background:var(--green-bg);border-color:var(--green-border)';
                    } elseif ($hasRPend) {
                    $bgStyle = 'background:var(--amber-bg);border-color:var(--amber-border)';
                    }
                    @endphp
                    <div class="rounded-xl border cursor-pointer transition-all duration-150 cal-day-cell p-1.5"
                        style="{{ $bgStyle }}"
                        data-date="{{ $dateStr }}"
                        data-has="{{ $hasAny ? '1' : '0' }}"
                        onclick="showDayDetail('{{ $dateStr }}')">

                        <div class="text-xs font-semibold text-center"
                            style="color:{{ $isToday ? 'var(--gold)' : 'var(--text-secondary)' }}">
                            {{ $day }}
                        </div>

                        {{-- ドット --}}
                        @if($hasAny)
                        <div class="flex justify-center gap-0.5 mt-1 flex-wrap">
                            @if($hasW)
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                style="background:var(--royal)"></span>
                            @endif
                            @if($hasM)
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                style="background:var(--gold)"></span>
                            @endif
                            @if($hasRConf)
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                style="background:var(--green)"></span>
                            @endif
                            @if($hasRPend)
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                style="background:var(--amber)"></span>
                            @endif
                        </div>
                        @endif

                        {{-- プレビューテキスト --}}
                        @if($hasW)
                        <p class="text-center mt-1 leading-tight"
                            style="font-size:8px;color:var(--royal)">
                            {{ $workoutsByDate[$dateStr]->first()->menu->name ?? '' }}
                            @if($workoutsByDate[$dateStr]->count() > 1)
                            +{{ $workoutsByDate[$dateStr]->count() - 1 }}
                            @endif
                        </p>
                        @elseif($hasM)
                        <p class="text-center mt-1 leading-tight"
                            style="font-size:8px;color:var(--gold)">
                            {{ $bodyStatsByDate[$dateStr]->first()->weight }}kg
                        </p>
                        @elseif($hasRConf || $hasRPend)
                        <p class="text-center mt-1 leading-tight"
                            style="font-size:8px;color:{{ $hasRConf ? 'var(--green-dark)' : 'var(--amber)' }}">
                            {{ $resGroup->first()->start_at->format('H:i') }}〜
                        </p>
                        @endif

                        @if($isToday)
                        <p class="text-center mt-0.5"
                            style="font-size:7px;color:var(--gold)">本日</p>
                        @endif
                    </div>
                    @endfor
            </div>

            {{-- 日付詳細パネル --}}
            <div id="dayDetailPanel" class="hidden mt-4 rounded-xl p-4"
                style="background:var(--blue-bg);border:1px solid var(--blue-border)">
                <div class="flex items-center justify-between mb-3">
                    <p id="detailTitle" class="text-sm font-semibold"
                        style="color:var(--royal)"></p>
                    <button onclick="hideDayDetail()"
                        class="text-xs px-2 py-1 rounded-lg"
                        style="color:var(--text-muted);border:1px solid var(--blue-border)">
                        閉じる
                    </button>
                </div>
                <div id="detailContent" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
            </div>
        </div>

        {{-- 体重推移グラフ --}}
        <div class="rounded-2xl p-5 bg-white" style="border:1px solid var(--card-border)">
            <div class="flex justify-between items-center mb-4">
                <p class="text-xs font-semibold tracking-widest uppercase"
                    style="color:var(--gold)">体重推移グラフ</p>
                <div class="flex gap-4 text-xs" style="color:var(--text-muted)">
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 rounded"
                            style="background:var(--royal)"></span>実績
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 rounded"
                            style="background:var(--gold);border-top:2px dashed var(--gold)"></span>予測
                    </span>
                    @if($client->target_weight)
                    <span class="flex items-center gap-1">
                        <span class="inline-block w-5 h-0.5 rounded"
                            style="background:var(--green)"></span>
                        目標 {{ $client->target_weight }}kg
                    </span>
                    @endif
                </div>
            </div>
            <div style="position:relative;height:280px">
                <canvas id="weightChart" role="img"
                    aria-label="{{ $client->name }}さんの体重推移グラフ"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    {{-- カレンダーデータをJSに渡す --}}
    @php
    $workouts = $client->workoutLogs
    ->whereBetween('logged_at', [$calStart, $calEnd])
    ->groupBy(fn($w) => \Carbon\Carbon::parse($w->logged_at)->format('Y-m-d'))
    ->map(fn($logs) => $logs->map(fn($l) => [
    'menu' => $l->menu->name ?? '—',
    'weight' => $l->weight,
    'reps' => $l->reps,
    'sets' => $l->sets,
    'vol' => $l->total_volume,
    'intensity' => $l->intensityLabel(),
    'notes' => $l->condition_notes,
    ])->values())
    ->toArray();

    $measures = $client->bodyStats
    ->whereBetween('measured_at', [$calStart, $calEnd])
    ->groupBy(fn($s) => \Carbon\Carbon::parse($s->measured_at)->format('Y-m-d'))
    ->map(fn($stats) => $stats->map(fn($s) => [
    'weight' => $s->weight,
    'fat' => $s->body_fat_percentage,
    'muscle' => $s->muscle_mass,
    'bmi' => $s->bmi,
    ])->values())
    ->toArray();

    $reservations = $client->reservations
    ->whereBetween('start_at', [$calStart, $calEnd])
    ->groupBy(fn($r) => \Carbon\Carbon::parse($r->start_at)->format('Y-m-d'))
    ->map(fn($res) => $res->map(fn($r) => [
    'start' => \Carbon\Carbon::parse($r->start_at)->format('H:i'),
    'end' => \Carbon\Carbon::parse($r->end_at)->format('H:i'),
    'status' => $r->status,
    'trainer' => $r->trainer->name ?? '—',
    ])->values())
    ->toArray();
    @endphp

    <script>
        const CAL_DATA = {
            workouts: @json($workouts),
            measures: @json($measures),
            reservations: @json($reservations),
        };

        function showDayDetail(dateStr) {
            const panel = document.getElementById('dayDetailPanel');
            const title = document.getElementById('detailTitle');
            const content = document.getElementById('detailContent');

            const d = new Date(dateStr);
            const label = d.toLocaleDateString('ja-JP', {
                month: 'long',
                day: 'numeric',
                weekday: 'short'
            });
            title.textContent = label + ' の記録';

            const workouts = CAL_DATA.workouts[dateStr] || [];
            const measures = CAL_DATA.measures[dateStr] || [];
            const reservations = CAL_DATA.reservations[dateStr] || [];

            let html = '';

            if (reservations.length > 0) {
                reservations.forEach(r => {
                    const statusColor = r.status === 1 ? '#16a34a' : '#d97706';
                    const statusLabel = r.status === 1 ? '確定' : r.status === 0 ? '仮予約' : 'キャンセル';
                    const bgColor = r.status === 1 ? '#f0fdf4' : '#fffbeb';
                    const bdColor = r.status === 1 ? '#86efac' : '#fde68a';
                    html += `
                <div style="background:#fff;border:1px solid var(--card-border);border-left:3px solid ${statusColor};border-radius:10px;padding:12px">
                    <p style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">予約</p>
                    <p style="font-size:13px;font-weight:600;color:var(--navy)">${r.start} 〜 ${r.end}</p>
                    <p style="font-size:11px;color:var(--text-muted);margin-top:2px">${r.trainer}</p>
                    <span style="display:inline-block;margin-top:4px;padding:1px 8px;border-radius:99px;font-size:10px;font-weight:600;background:${bgColor};color:${statusColor};border:1px solid ${bdColor}">${statusLabel}</span>
                </div>`;
                });
            }

            if (measures.length > 0) {
                measures.forEach(m => {
                    html += `
                <div style="background:#fff;border:1px solid var(--card-border);border-left:3px solid var(--gold);border-radius:10px;padding:12px">
                    <p style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">計測</p>
                    <p style="font-size:13px;font-weight:600;color:var(--navy)">${m.weight ?? '—'} kg</p>
                    <p style="font-size:11px;color:var(--text-muted);margin-top:2px">
                        体脂肪率 ${m.fat ?? '—'}%　筋肉量 ${m.muscle ?? '—'} kg　BMI ${m.bmi ?? '—'}
                    </p>
                </div>`;
                });
            }

            if (workouts.length > 0) {
                let wHtml = `
            <div style="background:#fff;border:1px solid var(--card-border);border-left:3px solid var(--royal);border-radius:10px;padding:12px;grid-column:1/-1">
                <p style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">トレーニング</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">`;
                workouts.forEach(w => {
                    wHtml += `
                <div style="background:var(--surface);border-radius:8px;padding:8px 10px">
                    <p style="font-size:12px;font-weight:600;color:var(--navy)">${w.menu}</p>
                    <p style="font-size:11px;color:var(--text-muted);margin-top:2px">
                        ${w.weight}kg × ${w.reps}回 × ${w.sets}セット
                    </p>
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <span style="font-size:10px;color:var(--text-muted)">総負荷</span>
                        <span style="font-size:11px;font-weight:600;color:var(--royal)">${Number(w.vol).toLocaleString()}kg</span>
                    </div>
                    ${w.notes ? `<p style="font-size:10px;color:var(--amber);margin-top:3px">📝 ${w.notes}</p>` : ''}
                </div>`;
                });
                wHtml += '</div></div>';
                html += wHtml;
            }

            if (!html) {
                html = `<div style="grid-column:1/-1;text-align:center;padding:16px;color:var(--text-muted);font-size:13px">この日の記録はありません</div>`;
            }

            content.innerHTML = html;
            panel.classList.remove('hidden');
            panel.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function hideDayDetail() {
            document.getElementById('dayDetailPanel').classList.add('hidden');
        }

        // 計測入力フォームの表示切替
        document.querySelectorAll('.stat-tap-card').forEach(card => {
            card.addEventListener('click', () => {
                const form = document.getElementById('measureForm');
                form.classList.toggle('hidden');
                if (!form.classList.contains('hidden')) {
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            });
        });
        document.getElementById('closeMeasureForm')?.addEventListener('click', () => {
            document.getElementById('measureForm').classList.add('hidden');
        });

        // 体重グラフ
        (function() {
            const actual = @json($progress['actual']);
            const forecast = @json($progress['forecast']);
            const target = {
                {
                    $client - > target_weight ?? 'null'
                }
            };
            const ctx = document.getElementById('weightChart');
            if (!ctx) return;

            const allW = [...actual.map(p => p.y), ...forecast.map(p => p.y), target].filter(v => v != null);
            const yMin = allW.length ? Math.floor(Math.min(...allW) - 2) : 40;
            const yMax = allW.length ? Math.ceil(Math.max(...allW) + 2) : 100;

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
                            borderColor: '#1a56a0',
                            backgroundColor: 'rgba(26,86,160,0.07)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#1a56a0',
                            borderWidth: 2.5,
                        },
                        {
                            label: '予測体重',
                            data: forecast,
                            parsing: {
                                xAxisKey: 'x',
                                yAxisKey: 'y'
                            },
                            borderColor: '#b8952a',
                            backgroundColor: 'transparent',
                            borderDash: [6, 4],
                            tension: 0.35,
                            pointRadius: 2,
                            pointBackgroundColor: '#b8952a',
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
                                color: '#8fa3b8',
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
                                color: '#8fa3b8',
                                font: {
                                    size: 11
                                },
                                callback: v => v + ' kg'
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
                        ctx.strokeStyle = '#22c55e';
                        ctx.lineWidth = 1.5;
                        ctx.setLineDash([4, 4]);
                        ctx.beginPath();
                        ctx.moveTo(x.left, yPos);
                        ctx.lineTo(x.right, yPos);
                        ctx.stroke();
                        ctx.fillStyle = '#22c55e';
                        ctx.font = '11px DM Sans, sans-serif';
                        ctx.fillText('目標 ' + target + 'kg', x.right - 78, yPos - 5);
                        ctx.restore();
                    },
                }],
            });
        })();
    </script>
</x-app-layout>