<div>
    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
        氏名 *
    </label>
    <input type="text" name="name"
           value="{{ old('name', $client?->name) }}"
           placeholder="山田 花子"
           class="w-full rounded-xl px-4 py-2.5 text-sm transition"
           style="border:1px solid var(--card-border);color:var(--navy)">
    @error('name')
    <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
            担当トレーナー *
        </label>
        <select name="trainer_id"
                class="w-full rounded-xl px-4 py-2.5 text-sm"
                style="border:1px solid var(--card-border);color:var(--navy)">
            @foreach($trainers as $trainer)
            <option value="{{ $trainer->id }}"
                {{ old('trainer_id', $client?->trainer_id) == $trainer->id ? 'selected' : '' }}>
                {{ $trainer->name }}
            </option>
            @endforeach
        </select>
        @error('trainer_id')
        <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
            性別 *
        </label>
        <div class="grid grid-cols-2 gap-2">
            @foreach([1 => '男性', 2 => '女性'] as $val => $label)
            <label class="flex items-center justify-center gap-2 rounded-xl py-2.5 cursor-pointer
                          transition-all duration-150 gender-btn"
                   style="border:2px solid var(--card-border);color:var(--text-muted)">
                <input type="radio" name="gender" value="{{ $val }}"
                       class="hidden gender-radio"
                       {{ old('gender', $client?->gender) == $val ? 'checked' : '' }}>
                <span>{{ $val === 1 ? '👨' : '👩' }}</span>
                <span class="text-sm font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>
        @error('gender')
        <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
            生年月日 *
        </label>
        <input type="date" name="birth_date"
               value="{{ old('birth_date', $client?->birth_date?->format('Y-m-d')) }}"
               class="w-full rounded-xl px-4 py-2.5 text-sm"
               style="border:1px solid var(--card-border);color:var(--navy)">
        @error('birth_date')
        <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
            身長 (cm) *
        </label>
        <input type="number" name="height" step="0.1" min="100" max="250"
               value="{{ old('height', $client?->height) }}"
               placeholder="158.0"
               class="w-full rounded-xl px-4 py-2.5 text-sm"
               style="border:1px solid var(--card-border);color:var(--navy)">
        @error('height')
        <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
            活動レベル (PAL) *
        </label>
        <div class="space-y-1.5">
            @foreach([
                [1, '低い (I)',   'デスクワーク中心'],
                [2, 'ふつう (II)','適度な活動'],
                [3, '高い (III)', '活発な運動習慣'],
            ] as [$val, $label, $sub])
            <label class="flex items-center gap-3 rounded-xl px-4 py-2.5 cursor-pointer
                          transition-all duration-150 pal-btn"
                   style="border:2px solid var(--card-border)">
                <input type="radio" name="pal_level" value="{{ $val }}"
                       class="hidden pal-radio"
                       {{ old('pal_level', $client?->pal_level) == $val ? 'checked' : '' }}>
                <div class="flex-1">
                    <p class="text-sm font-medium" style="color:var(--navy)">{{ $label }}</p>
                    <p class="text-xs" style="color:var(--text-muted)">{{ $sub }}</p>
                </div>
            </label>
            @endforeach
        </div>
        @error('pal_level')
        <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
                目標体重 (kg)
            </label>
            <input type="number" name="target_weight" step="0.1" min="30" max="200"
                   value="{{ old('target_weight', $client?->target_weight) }}"
                   placeholder="52.0"
                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                   style="border:1px solid var(--card-border);color:var(--navy)">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
                LINE ユーザーID
            </label>
            <input type="text" name="line_user_id"
                   value="{{ old('line_user_id', $client?->line_user_id) }}"
                   placeholder="Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                   style="border:1px solid var(--card-border);color:var(--navy)">
        </div>
    </div>
</div>

<div>
    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">
        メディカルノート
        <span class="font-normal ml-1" style="color:var(--text-muted)">
            （既往歴・アレルギー・注意事項）
        </span>
    </label>
    <textarea name="medical_notes" rows="3"
              placeholder="例：高血圧の既往あり。膝に持病があるため、スクワット系は軽負荷で実施。"
              class="w-full rounded-xl px-4 py-2.5 text-sm resize-none"
              style="border:1px solid var(--card-border);color:var(--navy)">{{ old('medical_notes', $client?->medical_notes) }}</textarea>
</div>

<script>
function applyRadioStyle(btnClass, radioClass, activeStyle, inactiveStyle) {
    document.querySelectorAll('.' + radioClass).forEach(r => {
        const btn = r.closest('.' + btnClass);
        if (!btn) return;
        const apply = () => {
            document.querySelectorAll('.' + radioClass).forEach(rr => {
                const b = rr.closest('.' + btnClass);
                if (b) b.style.cssText = rr.checked ? activeStyle : inactiveStyle;
            });
        };
        r.addEventListener('change', apply);
        if (r.checked) btn.style.cssText = activeStyle;
        else btn.style.cssText = inactiveStyle;
    });
}

applyRadioStyle(
    'gender-btn', 'gender-radio',
    'border-color:var(--royal);background:var(--blue-bg);color:var(--royal)',
    'border-color:var(--card-border);color:var(--text-muted)'
);
applyRadioStyle(
    'pal-btn', 'pal-radio',
    'border-color:var(--gold);background:var(--gold-bg)',
    'border-color:var(--card-border)'
);
</script>