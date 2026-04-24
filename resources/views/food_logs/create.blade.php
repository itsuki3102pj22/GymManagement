<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('food-logs.index', $client) }}"
               class="text-sm text-gray-400 hover:text-gray-600">
                ← {{ $client->name }} さんの食事ログ
            </a>
            <h2 class="font-semibold text-xl text-gray-800 mt-1">食事を記録</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('food-logs.store', $client) }}" method="POST"
                  class="space-y-5">
                @csrf

                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">記録日 *</label>
                        <input type="date" name="logged_at"
                               value="{{ old('logged_at', today()->format('Y-m-d')) }}"
                               class="border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            食事メモ *
                            <span class="text-gray-400 font-normal">（LINEの原文・自由記述）</span>
                        </label>
                        <textarea name="meal_text" rows="3"
                                  placeholder="例：朝：ごはん・卵・味噌汁　昼：鶏むね定食　夜：サーモン・ブロッコリー"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                         focus:outline-none focus:ring-2 focus:ring-blue-300">{{ old('meal_text') }}</textarea>
                        @error('meal_text')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 食品選択 --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm">
                        食品を選択して栄養計算
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">
                        食品を選択すると合計カロリー・PFCが自動計算されます。
                    </p>

                    {{-- 食品検索 --}}
                    <div class="mb-4">
                        <input type="text" id="foodSearch"
                               placeholder="食品名で絞り込み..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>

                    {{-- 食品チェックリスト --}}
                    <div id="foodList"
                         class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-y-auto
                                border border-gray-100 rounded-lg p-3">
                        @foreach($foods as $food)
                        <label class="food-item flex items-center gap-2 cursor-pointer
                                      hover:bg-gray-50 rounded px-2 py-1.5"
                               data-name="{{ $food->food_name }}">
                            <input type="checkbox" name="food_ids[]"
                                   value="{{ $food->id }}"
                                   class="food-checkbox rounded"
                                   data-calories="{{ $food->calories }}"
                                   data-protein="{{ $food->protein }}"
                                   data-fat="{{ $food->fat }}"
                                   data-carb="{{ $food->carb }}"
                                   data-name="{{ $food->food_name }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 truncate">
                                    {{ $food->food_name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $food->calories }}kcal |
                                    P:{{ $food->protein }}g
                                    F:{{ $food->fat }}g
                                    C:{{ $food->carb }}g
                                </p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <input type="number"
                                       name="quantities[]"
                                       value="1"
                                       min="0.1" max="10" step="0.1"
                                       class="w-14 border border-gray-200 rounded px-1.5 py-1
                                              text-xs text-center quantity-input
                                              focus:outline-none focus:ring-1 focus:ring-blue-300">
                                <span class="text-xs text-gray-400">倍</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- リアルタイム集計 --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                    <h3 class="font-semibold text-blue-700 mb-3 text-sm">
                        栄養素の合計（選択中）
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-400">合計カロリー</p>
                            <p class="text-xl font-bold text-gray-800" id="totalCalories">0</p>
                            <p class="text-xs text-gray-400">kcal</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-blue-400">たんぱく質</p>
                            <p class="text-xl font-bold text-blue-600" id="totalProtein">0</p>
                            <p class="text-xs text-gray-400">g（<span id="pPct">0</span>%）</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-amber-500">脂質</p>
                            <p class="text-xl font-bold text-amber-600" id="totalFat">0</p>
                            <p class="text-xs text-gray-400">g（<span id="fPct">0</span>%）</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-xs text-green-500">炭水化物</p>
                            <p class="text-xl font-bold text-green-600" id="totalCarb">0</p>
                            <p class="text-xs text-gray-400">g（<span id="cPct">0</span>%）</p>
                        </div>
                    </div>

                    {{-- EERとの比較 --}}
                    @if($eer)
                    <div class="mt-3 text-sm text-blue-600">
                        推奨カロリー：{{ number_format($eer) }} kcal／日
                        （差：<span id="calDiff">—</span> kcal）
                    </div>
                    @endif

                    {{-- PFC目標範囲バー --}}
                    <div class="mt-4 space-y-2">
                        @foreach([
                            ['id' => 'pBar', 'label' => 'P', 'color' => 'bg-blue-400',  'min' => 13, 'max' => 20],
                            ['id' => 'fBar', 'label' => 'F', 'color' => 'bg-amber-400', 'min' => 20, 'max' => 30],
                            ['id' => 'cBar', 'label' => 'C', 'color' => 'bg-green-400', 'min' => 50, 'max' => 65],
                        ] as $bar)
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="w-4 font-bold">{{ $bar['label'] }}</span>
                            <div class="flex-1 bg-white rounded-full h-3 overflow-hidden relative">
                                {{-- 目標範囲を薄くハイライト --}}
                                <div class="absolute h-full bg-gray-100 rounded-full"
                                     style="left:{{ $bar['min'] }}%;width:{{ $bar['max'] - $bar['min'] }}%">
                                </div>
                                <div id="{{ $bar['id'] }}"
                                     class="h-full {{ $bar['color'] }} rounded-full transition-all duration-300"
                                     style="width:0%"></div>
                            </div>
                            <span class="w-16 text-right">
                                目標{{ $bar['min'] }}〜{{ $bar['max'] }}%
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('food-logs.index', $client) }}"
                       class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50">
                        キャンセル
                    </a>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm
                                   font-medium hover:bg-blue-700 transition">
                        記録する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const EER = {{ $eer ?? 'null' }};

    function calcTotals() {
        let cal = 0, prot = 0, fat = 0, carb = 0;

        document.querySelectorAll('.food-checkbox:checked').forEach(cb => {
            const label = cb.closest('label');
            const qty   = parseFloat(label.querySelector('.quantity-input').value) || 1;
            cal  += parseFloat(cb.dataset.calories) * qty;
            prot += parseFloat(cb.dataset.protein)  * qty;
            fat  += parseFloat(cb.dataset.fat)      * qty;
            carb += parseFloat(cb.dataset.carb)     * qty;
        });

        const pPct = cal > 0 ? Math.round(prot * 4 / cal * 100) : 0;
        const fPct = cal > 0 ? Math.round(fat  * 9 / cal * 100) : 0;
        const cPct = cal > 0 ? Math.round(carb * 4 / cal * 100) : 0;

        document.getElementById('totalCalories').textContent = Math.round(cal);
        document.getElementById('totalProtein').textContent  = Math.round(prot * 10) / 10;
        document.getElementById('totalFat').textContent      = Math.round(fat  * 10) / 10;
        document.getElementById('totalCarb').textContent     = Math.round(carb * 10) / 10;
        document.getElementById('pPct').textContent = pPct;
        document.getElementById('fPct').textContent = fPct;
        document.getElementById('cPct').textContent = cPct;

        document.getElementById('pBar').style.width = Math.min(pPct, 100) + '%';
        document.getElementById('fBar').style.width = Math.min(fPct, 100) + '%';
        document.getElementById('cBar').style.width = Math.min(cPct, 100) + '%';

        if (EER !== null) {
            const diff = Math.round(cal) - EER;
            const el   = document.getElementById('calDiff');
            el.textContent = (diff >= 0 ? '+' : '') + diff;
            el.style.color = Math.abs(diff) <= 200 ? '#15803d' : '#dc2626';
        }
    }

    // チェックボックス・数量変更で再計算
    document.addEventListener('change', e => {
        if (e.target.classList.contains('food-checkbox') ||
            e.target.classList.contains('quantity-input')) {
            calcTotals();
        }
    });
    document.addEventListener('input', e => {
        if (e.target.classList.contains('quantity-input')) calcTotals();
    });

    // 食品名フィルター
    document.getElementById('foodSearch').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.food-item').forEach(item => {
            item.style.display = item.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    });
    </script>

</x-app-layout>