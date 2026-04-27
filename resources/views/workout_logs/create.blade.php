<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('clients.show', $client) }}"
                   class="text-sm transition" style="color:var(--text-muted)">
                    ← {{ $client->name }} さん
                </a>
                <h2 class="font-display mt-1"
                    style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                    トレーニング記録
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- 新規入力フォーム --}}
            <form action="{{ route('workout-logs.store', $client) }}"
                  method="POST" id="workoutForm">
                @csrf

                <div class="rounded-2xl p-5 bg-white"
                     style="border:1px solid var(--card-border);border-top:3px solid var(--royal)">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs font-semibold tracking-widest uppercase"
                           style="color:var(--royal)">新規トレーニング入力</p>
                        <div class="flex items-center gap-2">
                            <label class="text-xs" style="color:var(--text-muted)">セッション日</label>
                            <input type="date" name="logged_at"
                                   value="{{ old('logged_at', today()->format('Y-m-d')) }}"
                                   class="rounded-lg px-3 py-1.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="rounded-xl px-4 py-3 mb-4 text-sm"
                         style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div id="logRows" class="space-y-3"></div>

                    <button type="button" id="addRow"
                            class="w-full mt-3 py-3 rounded-xl text-sm transition-all duration-150"
                            style="border:2px dashed var(--blue-border);color:var(--royal);
                                   background:var(--blue-bg)"
                            onmouseover="this.style.borderColor='var(--royal)'"
                            onmouseout="this.style.borderColor='var(--blue-border)'">
                        + 種目を追加
                    </button>

                    <div class="flex justify-end gap-3 mt-4">
                        <a href="{{ route('clients.show', $client) }}"
                           class="px-5 py-2 rounded-lg text-sm transition"
                           style="border:1px solid var(--card-border);color:var(--text-secondary)">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="px-6 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background:var(--royal)">
                            記録を保存
                        </button>
                    </div>
                </div>
            </form>

            {{-- 直近の記録（クリックで編集） --}}
            @if($latestLogs->count() > 0)
            <div class="rounded-2xl overflow-hidden bg-white"
                 style="border:1px solid var(--card-border)">

                <div class="px-5 py-4 flex items-center justify-between"
                     style="border-bottom:1px solid var(--card-border);background:var(--surface)">
                    <p class="text-xs font-semibold tracking-widest uppercase"
                       style="color:var(--gold)">直近の記録</p>
                    <p class="text-xs" style="color:var(--text-muted)">
                        行をクリックすると編集・削除できます
                    </p>
                </div>

                {{-- 日付でグループ化して表示 --}}
                @foreach($latestLogs->groupBy(fn($l) => $l->logged_at->format('Y-m-d')) as $date => $logs)
                <div class="log-date-group">
                    {{-- 日付ヘッダー --}}
                    <div class="px-5 py-2 flex items-center gap-3"
                         style="background:var(--blue-bg);border-bottom:1px solid var(--blue-border)">
                        <span class="text-xs font-semibold" style="color:var(--royal)">
                            {{ \Carbon\Carbon::parse($date)->format('Y年m月d日（D）') }}
                        </span>
                        <span class="text-xs" style="color:var(--text-muted)">
                            {{ $logs->count() }} 種目
                            ／ 総負荷 {{ number_format($logs->sum('total_volume')) }} kg
                        </span>
                    </div>

                    {{-- その日のログ一覧 --}}
                    @foreach($logs as $log)
                    <div class="log-row px-5 py-3 cursor-pointer transition-all duration-150"
                         style="border-bottom:1px solid var(--card-border)"
                         data-id="{{ $log->id }}"
                         data-menu-id="{{ $log->menu_id }}"
                         data-menu-name="{{ $log->menu->name }}"
                         data-weight="{{ $log->weight }}"
                         data-reps="{{ $log->reps }}"
                         data-sets="{{ $log->sets }}"
                         data-intensity="{{ $log->intensity }}"
                         data-notes="{{ $log->condition_notes }}"
                         data-date="{{ $log->logged_at->format('Y-m-d') }}"
                         data-update-url="{{ route('workout-logs.update', [$client, $log]) }}"
                         data-delete-url="{{ route('workout-logs.destroy', [$client, $log]) }}"
                         onmouseover="this.style.background='var(--blue-bg)'"
                         onmouseout="this.style.background=''">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                {{-- 強度バッジ --}}
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold flex-shrink-0"
                                      style="background:{{ $log->intensity === 1 ? 'var(--blue-bg)' :
                                                         ($log->intensity === 2 ? '#fef9c3' : '#fef2f2') }};
                                             color:{{ $log->intensity === 1 ? 'var(--royal)' :
                                                     ($log->intensity === 2 ? '#a16207' : '#dc2626') }};
                                             border:1px solid {{ $log->intensity === 1 ? 'var(--blue-border)' :
                                                                ($log->intensity === 2 ? 'var(--amber-border)' : '#fecaca') }}">
                                    {{ $log->intensityLabel() }}
                                </span>
                                {{-- 種目名 --}}
                                <div class="min-w-0">
                                    <p class="font-semibold truncate"
                                       style="color:var(--navy);font-size:14px">
                                        {{ $log->menu->name }}
                                    </p>
                                    @if($log->condition_notes)
                                    <p class="text-xs truncate mt-0.5" style="color:var(--amber)">
                                        📝 {{ $log->condition_notes }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            {{-- 数値 --}}
                            <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                <div class="text-center">
                                    <p class="text-base font-bold" style="color:var(--navy)">
                                        {{ $log->weight }}
                                    </p>
                                    <p class="text-xs" style="color:var(--text-muted)">kg</p>
                                </div>
                                <div class="text-xs" style="color:var(--text-muted)">×</div>
                                <div class="text-center">
                                    <p class="text-base font-bold" style="color:var(--navy)">
                                        {{ $log->reps }}
                                    </p>
                                    <p class="text-xs" style="color:var(--text-muted)">回</p>
                                </div>
                                <div class="text-xs" style="color:var(--text-muted)">×</div>
                                <div class="text-center">
                                    <p class="text-base font-bold" style="color:var(--navy)">
                                        {{ $log->sets }}
                                    </p>
                                    <p class="text-xs" style="color:var(--text-muted)">set</p>
                                </div>
                                <div class="text-center pl-3"
                                     style="border-left:1px solid var(--card-border)">
                                    <p class="text-base font-bold" style="color:var(--royal)">
                                        {{ number_format($log->total_volume) }}
                                    </p>
                                    <p class="text-xs" style="color:var(--text-muted)">vol</p>
                                </div>
                                {{-- 編集アイコン --}}
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                            flex-shrink-0"
                                     style="background:var(--surface);border:1px solid var(--card-border)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" style="color:var(--text-muted)">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828
                                                 l8.586-8.586z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>

    {{-- 編集モーダル --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center"
         style="background:rgba(13,39,68,0.5);backdrop-filter:blur(4px)">
        <div class="rounded-2xl p-6 w-full max-w-lg mx-4 relative"
             style="background:#fff;border:1px solid var(--card-border);
                    box-shadow:0 20px 60px rgba(13,39,68,0.2)">

            {{-- モーダルヘッダー --}}
            <div class="flex items-center justify-between mb-5">
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase mb-0.5"
                       style="color:var(--gold)">トレーニング編集</p>
                    <p id="modalMenuName" class="font-display text-lg"
                       style="color:var(--navy);font-weight:400"></p>
                </div>
                <button onclick="closeEditModal()"
                        class="w-8 h-8 rounded-full flex items-center justify-center transition"
                        style="background:var(--surface);color:var(--text-muted)">
                    ✕
                </button>
            </div>

            <form id="editForm" class="space-y-4">
                @csrf
                <input type="hidden" id="editMethod" name="_method" value="PUT">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                            種目 *
                        </label>
                        <select id="editMenuId" name="menu_id"
                                class="w-full rounded-xl px-3 py-2 text-sm"
                                style="border:1px solid var(--card-border);color:var(--navy)">
                            @foreach(\App\Models\Menu::orderBy('category')->orderBy('name')->get()->groupBy('category') as $cat => $items)
                            <optgroup label="{{ $cat }}">
                                @foreach($items as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                            記録日 *
                        </label>
                        <input type="date" id="editDate" name="logged_at"
                               class="w-full rounded-xl px-3 py-2 text-sm"
                               style="border:1px solid var(--card-border);color:var(--navy)">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                            重量 (kg) *
                        </label>
                        <input type="number" id="editWeight" name="weight"
                               step="0.5" min="0" max="500"
                               class="w-full rounded-xl px-3 py-2 text-sm text-center font-semibold"
                               style="border:1px solid var(--card-border);color:var(--navy)">
                    </div>
                    <div>
                        <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                            回数 *
                        </label>
                        <input type="number" id="editReps" name="reps"
                               min="1" max="200"
                               class="w-full rounded-xl px-3 py-2 text-sm text-center font-semibold"
                               style="border:1px solid var(--card-border);color:var(--navy)">
                    </div>
                    <div>
                        <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                            セット数 *
                        </label>
                        <input type="number" id="editSets" name="sets"
                               min="1" max="20"
                               class="w-full rounded-xl px-3 py-2 text-sm text-center font-semibold"
                               style="border:1px solid var(--card-border);color:var(--navy)">
                    </div>
                </div>

                {{-- 総負荷プレビュー --}}
                <div class="rounded-xl p-3 text-center"
                     style="background:var(--blue-bg);border:1px solid var(--blue-border)">
                    <p class="text-xs mb-1" style="color:var(--text-muted)">総負荷（自動計算）</p>
                    <p class="text-2xl font-bold" style="color:var(--royal)">
                        <span id="volumePreview">0</span>
                        <span class="text-sm font-normal" style="color:var(--text-muted)"> kg</span>
                    </p>
                </div>

                <div>
                    <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                        体感強度 *
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([1 => '弱', 2 => '中', 3 => '強'] as $val => $label)
                        <label class="intensity-btn flex items-center justify-center gap-1.5
                                      rounded-xl py-2 cursor-pointer transition-all duration-150"
                               style="border:2px solid var(--card-border);color:var(--text-muted)">
                            <input type="radio" name="intensity" value="{{ $val }}"
                                   class="hidden intensity-radio">
                            <span>{{ ['1'=>'😊','2'=>'💪','3'=>'🔥'][$val] }}</span>
                            <span class="text-sm font-medium">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs mb-1.5" style="color:var(--text-muted)">
                        体調メモ
                    </label>
                    <input type="text" id="editNotes" name="condition_notes"
                           placeholder="睡眠不足、右肩に違和感 など"
                           class="w-full rounded-xl px-3 py-2 text-sm"
                           style="border:1px solid var(--card-border);color:var(--navy)">
                </div>

                {{-- エラー表示 --}}
                <div id="modalError" class="hidden rounded-xl px-4 py-3 text-sm"
                     style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626"></div>

                {{-- ボタン --}}
                <div class="flex gap-2 pt-1">
                    <button type="button" id="deleteBtn"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition"
                            style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca">
                        🗑 削除
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="flex-1 py-2 rounded-xl text-sm transition"
                            style="border:1px solid var(--card-border);color:var(--text-secondary)">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 rounded-xl text-sm font-semibold text-white transition"
                            style="background:var(--royal)">
                        更新する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // ===== 新規入力行の生成 =====
    const MENUS = @json(
        \App\Models\Menu::orderBy('category')->orderBy('name')
            ->get(['id','name','category'])
            ->groupBy('category')
    );

    let rowIndex = 0;

    function buildMenuOptions(selectedId = null) {
        let html = '<option value="">— 種目を選択 —</option>';
        for (const [cat, items] of Object.entries(MENUS)) {
            html += `<optgroup label="${cat}">`;
            for (const item of items) {
                const sel = String(item.id) === String(selectedId) ? 'selected' : '';
                html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
            }
            html += '</optgroup>';
        }
        return html;
    }

    function addRow(data = {}) {
        const i = rowIndex++;
        const row = document.createElement('div');
        row.className = 'rounded-xl p-4 relative';
        row.dataset.row = i;
        row.style.cssText = 'background:var(--surface);border:1px solid var(--card-border)';
        row.innerHTML = `
            <button type="button" onclick="removeRow(${i})"
                    class="absolute top-3 right-3 w-6 h-6 rounded-full flex items-center
                           justify-center text-sm transition"
                    style="background:var(--card-border);color:var(--text-muted)">×</button>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs mb-1" style="color:var(--text-muted)">種目 *</label>
                    <select name="logs[${i}][menu_id]"
                            class="w-full rounded-lg px-2 py-2 text-sm"
                            style="border:1px solid var(--card-border);color:var(--navy)">
                        ${buildMenuOptions(data.menu_id)}
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--text-muted)">重量 (kg) *</label>
                    <input type="number" name="logs[${i}][weight]" step="0.5" min="0" max="500"
                           value="${data.weight ?? ''}" placeholder="60.0"
                           class="w-full rounded-lg px-2 py-2 text-sm text-center"
                           style="border:1px solid var(--card-border);color:var(--navy)">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--text-muted)">回数 *</label>
                    <input type="number" name="logs[${i}][reps]" min="1" max="200"
                           value="${data.reps ?? ''}" placeholder="10"
                           class="w-full rounded-lg px-2 py-2 text-sm text-center"
                           style="border:1px solid var(--card-border);color:var(--navy)">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--text-muted)">セット *</label>
                    <input type="number" name="logs[${i}][sets]" min="1" max="20"
                           value="${data.sets ?? 3}" placeholder="3"
                           class="w-full rounded-lg px-2 py-2 text-sm text-center"
                           style="border:1px solid var(--card-border);color:var(--navy)">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--text-muted)">強度 *</label>
                    <select name="logs[${i}][intensity]"
                            class="w-full rounded-lg px-2 py-2 text-sm"
                            style="border:1px solid var(--card-border);color:var(--navy)">
                        <option value="1" ${data.intensity == 1 ? 'selected':''}>😊 弱</option>
                        <option value="2" ${!data.intensity || data.intensity == 2 ? 'selected':''}>💪 中</option>
                        <option value="3" ${data.intensity == 3 ? 'selected':''}>🔥 強</option>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <input type="text" name="logs[${i}][condition_notes]"
                       value="${data.condition_notes ?? ''}"
                       placeholder="体調メモ（任意）"
                       class="w-full rounded-lg px-3 py-1.5 text-sm"
                       style="border:1px solid var(--card-border);color:var(--navy)">
            </div>
        `;
        document.getElementById('logRows').appendChild(row);
    }

    function removeRow(i) {
        const rows = document.getElementById('logRows');
        const row  = rows.querySelector(`[data-row="${i}"]`);
        if (row) rows.removeChild(row);
    }

    addRow();
    document.getElementById('addRow').addEventListener('click', () => addRow());

    // ===== 編集モーダル =====
    let currentUpdateUrl = '';
    let currentDeleteUrl = '';
    let currentRowEl     = null;

    function openEditModal(rowEl) {
        currentUpdateUrl = rowEl.dataset.updateUrl;
        currentDeleteUrl = rowEl.dataset.deleteUrl;
        currentRowEl     = rowEl;

        document.getElementById('modalMenuName').textContent = rowEl.dataset.menuName;
        document.getElementById('editMenuId').value  = rowEl.dataset.menuId;
        document.getElementById('editDate').value    = rowEl.dataset.date;
        document.getElementById('editWeight').value  = rowEl.dataset.weight;
        document.getElementById('editReps').value    = rowEl.dataset.reps;
        document.getElementById('editSets').value    = rowEl.dataset.sets;
        document.getElementById('editNotes').value   = rowEl.dataset.notes || '';
        document.getElementById('modalError').classList.add('hidden');

        // 強度ラジオの設定
        const intensity = rowEl.dataset.intensity;
        document.querySelectorAll('.intensity-radio').forEach(r => {
            r.checked = r.value === intensity;
        });
        updateIntensityStyle();
        updateVolumePreview();

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
        currentRowEl = null;
    }

    // 総負荷プレビュー
    function updateVolumePreview() {
        const w = parseFloat(document.getElementById('editWeight').value) || 0;
        const r = parseInt(document.getElementById('editReps').value)    || 0;
        const s = parseInt(document.getElementById('editSets').value)    || 0;
        document.getElementById('volumePreview').textContent =
            Math.round(w * r * s).toLocaleString();
    }
    ['editWeight','editReps','editSets'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateVolumePreview);
    });

    // 強度ボタンのスタイル切替
    function updateIntensityStyle() {
        document.querySelectorAll('.intensity-btn').forEach(btn => {
            const radio = btn.querySelector('.intensity-radio');
            if (radio.checked) {
                const colors = {
                    '1': ['var(--blue-bg)',  'var(--royal)',  'var(--blue-border)'],
                    '2': ['#fef9c3',         '#a16207',       'var(--amber-border)'],
                    '3': ['#fef2f2',         '#dc2626',       '#fecaca'],
                };
                const [bg, color, border] = colors[radio.value] || colors['2'];
                btn.style.cssText = `background:${bg};color:${color};border-color:${border}`;
            } else {
                btn.style.cssText = 'background:var(--surface);color:var(--text-muted);border-color:var(--card-border)';
            }
        });
    }
    document.querySelectorAll('.intensity-radio').forEach(r => {
        r.addEventListener('change', updateIntensityStyle);
    });

    // ログ行クリックでモーダルを開く
    document.querySelectorAll('.log-row').forEach(row => {
        row.addEventListener('click', () => openEditModal(row));
    });

    // モーダル外クリックで閉じる
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // 更新送信
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const btn  = form.querySelector('[type=submit]');
        btn.textContent = '更新中...';
        btn.disabled    = true;

        const formData = new FormData(form);
        formData.set('_method', 'PUT');

        try {
            const res  = await fetch(currentUpdateUrl, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body:    formData,
            });
            const data = await res.json();

            if (data.success && currentRowEl) {
                // 画面上の数値を即時更新
                const w = parseFloat(document.getElementById('editWeight').value);
                const r = parseInt(document.getElementById('editReps').value);
                const s = parseInt(document.getElementById('editSets').value);
                const vol = Math.round(w * r * s);
                const intensity = form.querySelector('.intensity-radio:checked')?.value;

                currentRowEl.dataset.weight    = w;
                currentRowEl.dataset.reps      = r;
                currentRowEl.dataset.sets      = s;
                currentRowEl.dataset.intensity = intensity;
                currentRowEl.dataset.menuId    = document.getElementById('editMenuId').value;
                currentRowEl.dataset.notes     = document.getElementById('editNotes').value;

                // DOM更新
                const cells = currentRowEl.querySelectorAll('.text-base.font-bold');
                if (cells[0]) cells[0].textContent = w;
                if (cells[1]) cells[1].textContent = r;
                if (cells[2]) cells[2].textContent = s;
                if (cells[3]) cells[3].textContent = vol.toLocaleString();

                showToast(data.message, 'success');
                closeEditModal();
            } else {
                document.getElementById('modalError').textContent =
                    data.message || '更新に失敗しました。';
                document.getElementById('modalError').classList.remove('hidden');
            }
        } catch (err) {
            document.getElementById('modalError').textContent = '通信エラーが発生しました。';
            document.getElementById('modalError').classList.remove('hidden');
        } finally {
            btn.textContent = '更新する';
            btn.disabled    = false;
        }
    });

    // 削除
    document.getElementById('deleteBtn').addEventListener('click', async function() {
        if (!confirm(`「${currentRowEl?.dataset.menuName}」の記録を削除しますか？`)) return;

        this.textContent = '削除中...';
        this.disabled    = true;

        try {
            const res  = await fetch(currentDeleteUrl, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ _method: 'DELETE' }),
            });
            const data = await res.json();

            if (data.success && currentRowEl) {
                // 行を削除
                const group = currentRowEl.closest('.log-date-group');
                currentRowEl.remove();

                // その日のログが0件になったらグループごと削除
                if (group && group.querySelectorAll('.log-row').length === 0) {
                    group.remove();
                }

                showToast(data.message, 'success');
                closeEditModal();
            }
        } catch (err) {
            alert('削除に失敗しました。');
        } finally {
            this.textContent = '🗑 削除';
            this.disabled    = false;
        }
    });

    // ===== トースト通知 =====
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl text-sm font-medium shadow-lg';
        toast.style.cssText = type === 'success'
            ? 'background:var(--navy);color:#fff;border-left:4px solid var(--gold)'
            : 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca';
        toast.textContent = message;
        document.body.appendChild(toast);

        // フェードイン
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all .3s ease';
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        // 3秒後に消える
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    </script>

</x-app-layout>