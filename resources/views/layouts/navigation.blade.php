<nav x-data="{ open: false }" class="bg-white sticky top-0 z-50"
     style="border-bottom: 1px solid var(--card-border);">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-14">

            {{-- ロゴ --}}
            <a href="{{ route('dashboard') }}"
               class="font-display text-2xl tracking-widest"
               style="color:var(--navy);font-weight:400;letter-spacing:4px">
                PG<span style="color:var(--gold)">M</span>S
            </a>

            {{-- デスクトップナビ --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach([
                    ['route' => 'dashboard',        'label' => 'ダッシュボード'],
                    ['route' => 'clients.index',    'label' => '顧客管理'],
                    ['route' => 'reservations.index','label' => '予約'],
                    ['route' => 'food-master.index', 'label' => '食品マスタ'],
                    ['route' => 'menus.index',       'label' => '種目マスタ'],
                ] as $item)
                <a href="{{ route($item['route']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm transition-all duration-150"
                   style="{{ request()->routeIs(explode('.', $item['route'])[0].'.*') || request()->routeIs($item['route'])
                       ? 'background:var(--blue-bg);color:var(--royal);font-weight:600;'
                       : 'color:var(--text-secondary);' }}">
                    {{ $item['label'] }}
                </a>
                @endforeach

                @if(auth()->user()->isSupervisor())
                <a href="{{ route('users.index') }}"
                   class="px-3 py-1.5 rounded-lg text-sm transition-all duration-150"
                   style="{{ request()->routeIs('users.*')
                       ? 'background:var(--gold-bg);color:var(--gold);font-weight:600;'
                       : 'color:var(--text-secondary);' }}">
                    ユーザー管理
                </a>
                @endif
            </div>

            {{-- 右側 --}}
            <div class="hidden md:flex items-center gap-3">

                {{-- ロールバッジ --}}
                <span class="text-xs px-3 py-1 rounded-full font-semibold tracking-wide"
                      style="background:var(--gold-bg);color:var(--gold);border:1px solid var(--gold-light)">
                    {{ auth()->user()->isSupervisor() ? '責任者' : 'Trainer' }}
                </span>

                {{-- ユーザードロップダウン --}}
                <div class="relative" x-data="{ userOpen: false }">
                    <button @click="userOpen = !userOpen"
                            class="flex items-center gap-2 rounded-lg px-2 py-1 transition"
                            style="color:var(--text-secondary)">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    text-xs font-semibold text-white"
                             style="background:linear-gradient(135deg,var(--royal),var(--navy))">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium" style="color:var(--navy)">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="userOpen"
                         @click.away="userOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-52 rounded-xl py-1 z-50"
                         style="background:#fff;border:1px solid var(--card-border);
                                box-shadow:0 8px 24px rgba(13,39,68,0.10)">
                        <div class="px-4 py-2.5" style="border-bottom:1px solid var(--card-border)">
                            <p class="text-xs" style="color:var(--text-muted)">ログイン中</p>
                            <p class="text-sm font-medium truncate" style="color:var(--navy)">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm transition"
                           style="color:var(--text-secondary)"
                           onmouseover="this.style.background='var(--surface)'"
                           onmouseout="this.style.background=''">
                            プロフィール設定
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm transition"
                                    style="color:#dc2626"
                                    onmouseover="this.style.background='#fef2f2'"
                                    onmouseout="this.style.background=''">
                                ログアウト
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- モバイルハンバーガー --}}
            <button @click="open = !open" class="md:hidden p-2 rounded-lg"
                    style="color:var(--text-muted)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- モバイルメニュー --}}
    <div x-show="open" x-transition
         style="border-top:1px solid var(--card-border);background:#fff">
        <div class="px-4 py-3 space-y-1">
            @foreach([
                ['route' => 'dashboard',         'label' => 'ダッシュボード'],
                ['route' => 'clients.index',     'label' => '顧客管理'],
                ['route' => 'reservations.index','label' => '予約'],
                ['route' => 'food-master.index', 'label' => '食品マスタ'],
                ['route' => 'menus.index',       'label' => '種目マスタ'],
            ] as $item)
            <a href="{{ route($item['route']) }}"
               class="block px-3 py-2 rounded-lg text-sm"
               style="{{ request()->routeIs(explode('.', $item['route'])[0].'.*') || request()->routeIs($item['route'])
                   ? 'background:var(--blue-bg);color:var(--royal);font-weight:600;'
                   : 'color:var(--text-secondary);' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
            @if(auth()->user()->isSupervisor())
            <a href="{{ route('users.index') }}"
               class="block px-3 py-2 rounded-lg text-sm"
               style="color:var(--gold)">
                ユーザー管理
            </a>
            @endif
            <div style="border-top:1px solid var(--card-border);padding-top:8px;margin-top:8px">
                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 rounded-lg text-sm"
                   style="color:var(--text-secondary)">
                    プロフィール設定
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm"
                            style="color:#dc2626">
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>