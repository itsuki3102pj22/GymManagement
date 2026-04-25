<div>
    <label class="block text-sm text-gray-600 mb-1">名前 *</label>
    <input type="text" name="name"
           value="{{ old('name', $user?->name) }}"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                  focus:outline-none focus:ring-2 focus:ring-blue-300">
    @error('name')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">メールアドレス *</label>
    <input type="email" name="email"
           value="{{ old('email', $user?->email) }}"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                  focus:outline-none focus:ring-2 focus:ring-blue-300">
    @error('email')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">ロール *</label>
    <select name="role"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-300">
        <option value="1"
            {{ old('role', $user?->role) == 1 ? 'selected' : '' }}>
            トレーナー（担当顧客のみ管理）
        </option>
        <option value="2"
            {{ old('role', $user?->role) == 2 ? 'selected' : '' }}>
            責任者（全データ閲覧・操作）
        </option>
    </select>
    @error('role')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>