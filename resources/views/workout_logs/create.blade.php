<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('clients.show', $client) }}"
                    class="text-sm text-gray-400 hover:text-gray-600">
                    ← {{ $client->name }} さん
                </a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">
                    トレーニング記録
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('workout-logs.store', $client) }}"
                method="POST" id="workoutForm">
                @csrf

                {{-- セッション日付 --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-medium text-gray-600 whitespace-nowrap">
                            セッション日
                        </label>
                        <input type="date" name="logged_at"
                            value="{{ old('logged_at', today()->format('Y-m-d')) }}"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                {{-- エラー表示 --}}
                @if($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 種目入力行 --}}
                <div id="logRows" class="space-y-3">
                    {{-- JS で行を追加 --}}
                </div>

                {{-- 行追加ボタン --}}
                <button type="button" id="addRow"
                    class="w-full border-2 border-dashed border-gray-200 rounded-xl
                               py-3 text-sm text-gray-400 hover:border-blue-300
                               hover:text-blue-500 transition">
                    + 種目を追加
                </button>

                {{-- 保存ボタン --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('clients.show', $client) }}"
                        class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg
                              text-sm hover:bg-gray-50">
                        キャンセル
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm
                                   font-medium hover:bg-blue-700 transition">
                        記録を保存
                    </button>
                </div>
            </form>

            {{-- 前回のログ（参考表示） --}}
            @if($latestLogs->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    直近の記録（参考）
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
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($latestLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-400 text-xs">
                                    {{ $log->logged_at->format('m/d') }}
                                </td>
                                <td class="py-2 font-medium text-gray-700">
                                    {{ $log->menu->name }}
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->weight }}kg
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->reps }}回
                                </td>
                                <td class="py-2 text-right text-gray-600">
                                    {{ $log->sets }}セット
                                </td>
                                <td class="py-2 text-right text-gray-500 text-xs">
                                    {{ number_format($log->total_volume) }}kg
                                </td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $log->intensity === 1 ? 'bg-blue-100 text-blue-600' :
                                           ($log->intensity === 2 ? 'bg-yellow-100 text-yellow-700'
                                            : 'bg-red-100 text-red-600') }}">
                                        {{ $log->intensityLabel() }}
                                    </span>
                                </td>
                                <td class="py-2 text-right">
                                    <button type="button"
                                        class="text-xs text-blue-400 hover:text-blue-600 copy-btn"
                                        data-menu="{{ $log->menu_id }}"
                                        data-weight="{{ $log->weight }}"
                                        data-reps="{{ $log->reps }}"
                                        data-sets="{{ $log->sets }}"
                                        data-intensity="{{ $log->intensity }}">
                                        コピー
                                    </button>
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

    {{-- 種目データをJSに渡す --}}
    <script>
        const MENUS = @json($menus);

        let rowIndex = 0;

        function buildMenuOptions(selectedId = null) {
            let html = '<option value="">— 種目を選択 —</option>';
            for (const [cat, items] of Object.entries(MENUS)) {
                html += `<optgroup label="${cat}">`;
                for (const item of items) {
                    const sel = item.id == selectedId ? 'selected' : '';
                    html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
                }
                html += '</optgroup>';
            }
            return html;
        }

        function addRow(data = {}) {
            const i = rowIndex++;
            const row = document.createElement('div');
            row.className = 'bg-white rounded-xl shadow-sm p-5 relative';
            row.dataset.row = i;
            row.innerHTML = `
            <button type="button" onclick="removeRow(${i})"
                    class="absolute top-3 right-3 text-gray-300 hover:text-red-400 text-lg leading-none">
                ×
            </button>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <div class="col-span-2 md:col-span-2">
                    <label class="block text-xs text-gray-400 mb-1">種目 *</label>
                    <select name="logs[${i}][menu_id]"
                            class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-300">
                        ${buildMenuOptions(data.menu_id)}
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">重量 (kg) *</label>
                    <input type="number" name="logs[${i}][weight]"
                           step="0.5" min="0" max="500"
                           value="${data.weight ?? ''}"
                           placeholder="10.0"
                           class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">回数 *</label>
                    <input type="number" name="logs[${i}][reps]"
                           min="1" max="200"
                           value="${data.reps ?? ''}"
                           placeholder="10"
                           class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">セット数 *</label>
                    <input type="number" name="logs[${i}][sets]"
                           min="1" max="20"
                           value="${data.sets ?? 3}"
                           placeholder="3"
                           class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">体感強度 *</label>
                    <select name="logs[${i}][intensity]"
                            class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="1" ${data.intensity == 1 ? 'selected':''}>弱</option>
                        <option value="2" ${data.intensity == 2 ? 'selected': !data.intensity ? 'selected':''}> 中</option>
                        <option value="3" ${data.intensity == 3 ? 'selected':''}>強</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-xs text-gray-400 mb-1">体調メモ</label>
                <input type="text" name="logs[${i}][condition_notes]"
                       value="${data.condition_notes ?? ''}"
                       placeholder="睡眠不足、右膝に違和感 など"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
        `;
            document.getElementById('logRows').appendChild(row);
        }

        function removeRow(i) {
            const rows = document.getElementById('logRows');
            const row = rows.querySelector(`[data-row="${i}"]`);
            if (row) rows.removeChild(row);
        }

        // 初期行を1つ追加
        addRow();

        document.getElementById('addRow').addEventListener('click', () => addRow());

        // 前回ログのコピーボタン
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('copy-btn')) return;
            addRow({
                menu_id: e.target.dataset.menu,
                weight: e.target.dataset.weight,
                reps: e.target.dataset.reps,
                sets: e.target.dataset.sets,
                intensity: e.target.dataset.intensity,
            });
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

</x-app-layout>