<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">顧客一覧</h2>
            <a href="{{ route('clients.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                + 新規登録
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">氏名</th>
                            <th class="px-6 py-3 text-left">担当トレーナー</th>
                            <th class="px-6 py-3 text-left">最終計測</th>
                            <th class="px-6 py-3 text-left">現在の体重</th>
                            <th class="px-6 py-3 text-left">目標体重</th>
                            <th class="px-6 py-3 text-left">公開URL</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $client->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $client->trainer->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $client->latestBodyStat
                                    ? $client->latestBodyStat->measured_at->format('Y/m/d')
                                    : '未計測' }}
                            </td>
                            <td class="px-6 py-4 text-gray-800">
                                {{ $client->latestBodyStat
                                    ? $client->latestBodyStat->weight . ' kg'
                                    : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $client->target_weight
                                    ? $client->target_weight . ' kg'
                                    : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('public.progress', $client->uuid) }}"
                                   target="_blank"
                                   class="text-blue-500 hover:underline text-xs">
                                    リンクを開く ↗
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    詳細 →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                顧客が登録されていません。
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>