<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            責任者メニュー
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ジム全体サマリー --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">トレーナー数</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_trainers'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">総顧客数</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_clients'] }}</p>
                    <p class="text-xs text-gray-400">アクティブ {{ $stats['active_clients'] }}名</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">今月の売上</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">
                        ¥{{ number_format($stats['monthly_revenue']) }}
                    </p>
                </div>
            </div>

            {{-- トレーナー別顧客数 --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">トレーナー別担当顧客数</h3>
                <div class="divide-y">
                    @foreach($trainers as $trainer)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $trainer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $trainer->email }}</p>
                        </div>
                        <span class="text-lg font-bold text-indigo-600">
                            {{ $trainer->clients_count }} 名
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>