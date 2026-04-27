<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('clients.show', $client) }}"
                   class="text-sm text-gray-400 hover:text-gray-600">
                    ← {{ $client->name }} さん
                </a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">食事ログ</h2>
            </div>
            <a href="{{ route('food-logs.create', $client) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                + 食事を記録
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse($logs as $log)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $log->logged_at->format('Y年m月d日') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $log->meal_text }}</p>
                    </div>
                    <form action="{{ route('food-logs.destroy', [$client, $log]) }}"
                          method="POST"
                          onsubmit="return confirm('この食事ログを削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-400 hover:text-red-600 text-xs">
                            削除
                        </button>
                    </form>
                </div>

                @if($log->total_calories)
                <div class="grid grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400">合計</p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ number_format($log->total_calories) }}
                        </p>
                        <p class="text-xs text-gray-400">kcal</p>
                        @if($eer)
                        @php $diff = $log->total_calories - $eer; @endphp
                        <p class="text-xs {{ abs($diff) <= 200 ? 'text-green-500' : 'text-red-500' }} mt-1">
                            {{ $diff >= 0 ? '+' : '' }}{{ $diff }} kcal
                        </p>
                        @endif
                    </div>
                    @foreach([
                        ['label' => 'P', 'value' => $log->p_balance, 'grams' => $log->protein_grams, 'ok' => $log->p_balance >= 13 && $log->p_balance <= 20, 'color' => 'text-blue-600'],
                        ['label' => 'F', 'value' => $log->f_balance, 'grams' => $log->fat_grams, 'ok' => $log->f_balance >= 20 && $log->f_balance <= 30, 'color' => 'text-amber-600'],
                        ['label' => 'C', 'value' => $log->c_balance, 'grams' => $log->carbs_grams, 'ok' => $log->c_balance >= 50 && $log->c_balance <= 65, 'color' => 'text-green-600'],
                    ] as $pfc)
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-400">{{ $pfc['label'] }}</p>
                        <p class="text-lg font-bold {{ $pfc['color'] }}">
                            {{ $pfc['value'] }}%
                        </p>
                        @if($pfc['grams'] !== null && $pfc['grams'] > 0)
                        <p class="text-xs text-gray-500">{{ round($pfc['grams'], 1) }}g</p>
                        @endif
                        <p class="text-xs {{ $pfc['ok'] ? 'text-green-500' : 'text-red-400' }}">
                            {{ $pfc['ok'] ? '◎ 目標範囲' : '△ 要調整' }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
                食事ログがありません。
            </div>
            @endforelse

            <div>{{ $logs->links() }}</div>

        </div>
    </div>
</x-app-layout>