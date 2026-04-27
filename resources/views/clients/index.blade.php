<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display"
                style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                顧客一覧
            </h2>
            <a href="{{ route('clients.create') }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold text-white"
               style="background:var(--royal)">
                + 新規登録
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl overflow-hidden bg-white"
                 style="border:1px solid var(--card-border)">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:var(--surface);border-bottom:1px solid var(--card-border)">
                            @foreach(['氏名','担当トレーナー','最終計測','現在の体重','目標体重','公開URL',''] as $h)
                            <th class="px-5 py-3 text-left text-xs font-semibold tracking-widest uppercase"
                                style="color:var(--text-muted)">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr style="border-bottom:1px solid var(--card-border)"
                            onmouseover="this.style.background='var(--surface)'"
                            onmouseout="this.style.background=''">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                flex-shrink-0 font-display text-base"
                                         style="background:var(--blue-bg);color:var(--royal);
                                                border:1px solid var(--blue-border)">
                                        {{ mb_substr($client->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold" style="color:var(--navy)">
                                        {{ $client->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4" style="color:var(--text-secondary)">
                                {{ $client->trainer->name }}
                            </td>
                            <td class="px-5 py-4">
                                @if($client->latestBodyStat)
                                <span style="color:var(--text-secondary)">
                                    {{ $client->latestBodyStat->measured_at->format('Y/m/d') }}
                                </span>
                                @else
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                      style="background:var(--amber-bg);color:#d97706">未計測</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-semibold" style="color:var(--navy)">
                                {{ $client->latestBodyStat
                                    ? $client->latestBodyStat->weight . ' kg' : '—' }}
                            </td>
                            <td class="px-5 py-4 font-medium" style="color:var(--gold)">
                                {{ $client->target_weight
                                    ? $client->target_weight . ' kg' : '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('public.progress', $client->uuid) }}"
                                   target="_blank"
                                   class="text-xs transition" style="color:var(--royal)">
                                    リンク ↗
                                </a>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                                   style="background:var(--blue-bg);color:var(--royal);
                                          border:1px solid var(--blue-border)">
                                    詳細 →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center"
                                style="color:var(--text-muted)">
                                <p style="font-size:32px;margin-bottom:8px">👥</p>
                                顧客が登録されていません
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>