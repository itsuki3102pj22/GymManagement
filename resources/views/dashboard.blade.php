<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-display"
                    style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                    Dashboard
                </h2>
                <p class="text-sm mt-0.5" style="color:var(--text-muted)">
                    {{ now()->format('Y年m月d日（D）') }}
                </p>
            </div>
            <a href="{{ route('clients.create') }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
               style="background:var(--royal)">
                + 顧客登録
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- サマリーカード --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['label' => '担当顧客', 'value' => $stats['active_clients'], 'unit' => '名', 'icon' => '👥', 'color' => 'var(--royal)'],
                    ['label' => '本日の予約', 'value' => $todayReservations->count(), 'unit' => '件', 'icon' => '📅', 'color' => 'var(--navy)'],
                    ['label' => '確定済み', 'value' => $todayReservations->where('status',1)->count(), 'unit' => '件', 'icon' => '✅', 'color' => 'var(--green-dark)'],
                    ['label' => 'フォロー要', 'value' => $needFollowUp->count(), 'unit' => '名', 'icon' => $needFollowUp->count() > 0 ? '⚠️' : '✨', 'color' => $needFollowUp->count() > 0 ? '#dc2626' : 'var(--green-dark)'],
                ] as $card)
                <div class="rounded-2xl p-5 bg-white"
                     style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                    <div class="flex justify-between items-start mb-3">
                        <p class="text-xs font-semibold tracking-widest uppercase"
                           style="color:var(--text-muted)">{{ $card['label'] }}</p>
                        <span style="font-size:18px">{{ $card['icon'] }}</span>
                    </div>
                    <p class="text-3xl font-bold" style="color:{{ $card['color'] }}">
                        {{ $card['value'] }}
                    </p>
                    <p class="text-xs mt-1" style="color:var(--text-muted)">{{ $card['unit'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- 本日の予約 --}}
                <div class="rounded-2xl p-5 bg-white" style="border:1px solid var(--card-border)">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-xs font-semibold tracking-widest uppercase"
                           style="color:var(--gold)">本日の予約</p>
                        <a href="{{ route('reservations.index') }}"
                           class="text-xs transition" style="color:var(--royal)">
                            カレンダーへ →
                        </a>
                    </div>
                    @forelse($todayReservations as $r)
                    <div class="flex items-center gap-3 py-3"
                         style="border-bottom:1px solid var(--card-border)">
                        <div class="w-1 h-10 rounded-full flex-shrink-0"
                             style="background:{{ $r->status === 1 ? 'var(--green)' :
                                               ($r->status === 0 ? 'var(--amber)' : 'var(--card-border)') }}">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate" style="color:var(--navy);font-size:14px">
                                {{ $r->client->name }}
                            </p>
                            <p class="text-xs mt-0.5" style="color:var(--text-muted)">
                                {{ $r->trainer->name }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-medium" style="color:var(--navy)">
                                {{ $r->start_at->format('H:i') }}〜{{ $r->end_at->format('H:i') }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background:{{ $r->status === 1 ? 'var(--green-bg)' :
                                                      ($r->status === 0 ? 'var(--amber-bg)' : 'var(--surface)') }};
                                         color:{{ $r->status === 1 ? 'var(--green-dark)' :
                                                 ($r->status === 0 ? '#d97706' : 'var(--text-muted)') }}">
                                {{ $r->statusLabel() }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center" style="color:var(--text-muted)">
                        <p style="font-size:32px;margin-bottom:8px">📭</p>
                        <p class="text-sm">本日の予約はありません</p>
                    </div>
                    @endforelse
                </div>

                {{-- フォロー要 + クイックアクション --}}
                <div class="space-y-4">
                    @if($needFollowUp->count() > 0)
                    <div class="rounded-2xl p-5"
                         style="background:#fff8f0;border:1px solid #f0d9b8;border-left:4px solid var(--gold)">
                        <p class="text-xs font-semibold tracking-widest uppercase mb-3"
                           style="color:var(--gold)">⚠ フォローアップが必要</p>
                        <div class="space-y-2">
                            @foreach($needFollowUp->take(5) as $c)
                            <div class="flex items-center justify-between">
                                <a href="{{ route('clients.show', $c) }}"
                                   class="text-sm font-medium transition"
                                   style="color:var(--royal)">
                                    {{ $c->name }}
                                </a>
                                <span class="text-xs" style="color:var(--text-muted)">
                                    {{ $c->latestBodyStat
                                        ? $c->latestBodyStat->measured_at->format('Y/m/d')
                                        : '未計測' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="rounded-2xl p-5 text-center"
                         style="background:var(--green-bg);border:1px solid var(--green-border)">
                        <p style="font-size:28px;margin-bottom:6px">✨</p>
                        <p class="text-sm font-medium" style="color:var(--green-dark)">
                            全員の計測データが最新です
                        </p>
                    </div>
                    @endif

                    <div class="rounded-2xl p-5 bg-white" style="border:1px solid var(--card-border)">
                        <p class="text-xs font-semibold tracking-widest uppercase mb-3"
                           style="color:var(--gold)">クイックアクション</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['route' => 'clients.create',      'icon' => '👤', 'label' => '顧客登録'],
                                ['route' => 'reservations.index',  'icon' => '📅', 'label' => '予約管理'],
                                ['route' => 'food-master.index',   'icon' => '🥗', 'label' => '食品マスタ'],
                                ['route' => 'menus.index',         'icon' => '🏋️', 'label' => '種目マスタ'],
                            ] as $action)
                            <a href="{{ route($action['route']) }}"
                               class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm
                                      transition-all duration-150"
                               style="border:1px solid var(--card-border);color:var(--text-secondary)"
                               onmouseover="this.style.background='var(--blue-bg)';this.style.borderColor='var(--blue-border)';this.style.color='var(--royal)'"
                               onmouseout="this.style.background='';this.style.borderColor='var(--card-border)';this.style.color='var(--text-secondary)'">
                                <span style="font-size:16px">{{ $action['icon'] }}</span>
                                {{ $action['label'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>