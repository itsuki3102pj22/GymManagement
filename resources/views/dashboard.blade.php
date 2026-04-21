<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- サマリーカード --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">担当顧客数</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['active_clients'] }}</p>
                    <p class="text-xs text-gray-400">全{{ $stats['total_clients'] }}名</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">本日のセッション</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['today_sessions'] }}</p>
                    <p class="text-xs text-gray-400">件</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">今月の新規</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['new_this_month'] }}</p>
                    <p class="text-xs text-gray-400">名</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">ロール</p>
                    <p class="text-xl font-bold mt-2">
                        @if($user->isSupervisor())
                            <span class="text-purple-600">責任者</span>
                        @else
                            <span class="text-blue-600">トレーナー</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- 本日の予約 --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">本日の予約</h3>
                @if($todayReservations->isEmpty())
                    <p class="text-gray-400 text-sm">本日の予約はありません。</p>
                @else
                    <div class="divide-y">
                        @foreach($todayReservations as $r)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $r->client->name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $r->start_at->format('H:i') }} 〜 {{ $r->end_at->format('H:i') }}
                                </p>
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full
                                {{ $r->status === 1 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $r->statusLabel() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- クイックリンク --}}
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('clients.index') }}"
                   class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    顧客一覧
                </a>
                <a href="{{ route('clients.create') }}"
                   class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    + 顧客を登録
                </a>
                @if($user->isSupervisor())
                <a href="{{ route('supervisor.index') }}"
                   class="bg-purple-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                    責任者メニュー
                </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>