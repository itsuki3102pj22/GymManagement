<div>
    <label class="block text-sm text-gray-600 mb-1">氏名 *</label>
    <input type="text" name="name"
           value="{{ old('name', $client?->name) }}"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                  focus:outline-none focus:ring-2 focus:ring-blue-300">
    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">担当トレーナー *</label>
        <select name="trainer_id"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-300">
            @foreach($trainers as $trainer)
            <option value="{{ $trainer->id }}"
                {{ old('trainer_id', $client?->trainer_id) == $trainer->id ? 'selected' : '' }}>
                {{ $trainer->name }}
            </option>
            @endforeach
        </select>
        @error('trainer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">性別 *</label>
        <select name="gender"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="1" {{ old('gender', $client?->gender) == 1 ? 'selected' : '' }}>男性</option>
            <option value="2" {{ old('gender', $client?->gender) == 2 ? 'selected' : '' }}>女性</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">生年月日 *</label>
        <input type="date" name="birth_date"
               value="{{ old('birth_date', $client?->birth_date?->format('Y-m-d')) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-300">
        @error('birth_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">身長 (cm) *</label>
        <input type="number" name="height" step="0.1" min="100" max="250"
               value="{{ old('height', $client?->height) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-300">
        @error('height')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">活動レベル (PAL) *</label>
        <select name="pal_level"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="1" {{ old('pal_level', $client?->pal_level) == 1 ? 'selected' : '' }}>
                低い (I) — デスクワーク中心
            </option>
            <option value="2" {{ old('pal_level', $client?->pal_level) == 2 ? 'selected' : '' }}>
                ふつう (II) — 適度な活動
            </option>
            <option value="3" {{ old('pal_level', $client?->pal_level) == 3 ? 'selected' : '' }}>
                高い (III) — 活発な運動習慣
            </option>
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">目標体重 (kg)</label>
        <input type="number" name="target_weight" step="0.1" min="30" max="200"
               value="{{ old('target_weight', $client?->target_weight) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-300">
    </div>
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">
        メディカルノート
        <span class="text-gray-400 font-normal">（既往歴・アレルギー・注意事項）</span>
    </label>
    <textarea name="medical_notes" rows="3"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                     focus:outline-none focus:ring-2 focus:ring-blue-300"
              placeholder="例：高血圧の既往あり。膝に持病があるため、スクワット系は軽負荷で実施。">{{ old('medical_notes', $client?->medical_notes) }}</textarea>
</div>