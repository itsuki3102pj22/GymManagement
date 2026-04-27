<div>
    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
        名前 *
    </label>
    <input type="text" name="name"
           value="{{ old('name', $user?->name) }}"
           class="w-full rounded-xl px-4 py-2.5 text-sm"
           style="border:1px solid var(--card-border);color:var(--navy)">
    @error('name')
    <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
        メールアドレス *
    </label>
    <input type="email" name="email"
           value="{{ old('email', $user?->email) }}"
           class="w-full rounded-xl px-4 py-2.5 text-sm"
           style="border:1px solid var(--card-border);color:var(--navy)">
    @error('email')
    <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
        ロール *
    </label>
    <div class="grid grid-cols-2 gap-3">
        @foreach([
            [1, 'トレーナー',  '担当顧客のみ管理',  'var(--royal)',  'var(--blue-bg)',  'var(--blue-border)'],
            [2, '責任者',      '全データ閲覧・操作', 'var(--gold)',   'var(--gold-bg)',  'var(--gold-light)'],
        ] as [$val, $label, $sub, $color, $bg, $border])
        <label class="rounded-xl px-4 py-3 cursor-pointer transition-all duration-150 role-btn"
               style="border:2px solid var(--card-border)">
            <input type="radio" name="role" value="{{ $val }}"
                   class="hidden role-radio"
                   data-color="{{ $color }}" data-bg="{{ $bg }}" data-border="{{ $border }}"
                   {{ old('role', $user?->role) == $val ? 'checked' : '' }}>
            <p class="text-sm font-semibold" style="color:var(--navy)">{{ $label }}</p>
            <p class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $sub }}</p>
        </label>
        @endforeach
    </div>
    @error('role')
    <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
    @enderror
</div>

<script>
(function() {
    function updateRoleStyle() {
        document.querySelectorAll('.role-radio').forEach(r => {
            const btn = r.closest('.role-btn');
            if (!btn) return;
            if (r.checked) {
                btn.style.borderColor = r.dataset.border;
                btn.style.background  = r.dataset.bg;
            } else {
                btn.style.borderColor = 'var(--card-border)';
                btn.style.background  = '';
            }
        });
    }
    document.querySelectorAll('.role-radio').forEach(r => {
        r.addEventListener('change', updateRoleStyle);
    });
    updateRoleStyle();
})();
</script>