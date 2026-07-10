<div
    class="min-h-screen"
    x-data="{ mobileNavOpen: false }"
    @keydown.escape.window="mobileNavOpen = false"
    x-effect="
        const mobile = window.matchMedia('(max-width: 1023px)').matches;
        document.body.classList.toggle('ek-body--mobile-nav-open', mobileNavOpen && mobile);
        if (mobileNavOpen && mobile && $refs.headerInner && $refs.appHeader) {
            $refs.appHeader.style.setProperty('--ek-header-height', `${$refs.headerInner.offsetHeight}px`);
        }
    "
    @resize.window="
        if (mobileNavOpen && $refs.headerInner && $refs.appHeader) {
            $refs.appHeader.style.setProperty('--ek-header-height', `${$refs.headerInner.offsetHeight}px`);
        }
    "
>
    <header class="ek-app-header" x-ref="appHeader" :class="{ 'ek-app-header--nav-open': mobileNavOpen }">
        <div class="ek-app-header__inner" x-ref="headerInner">
            <div class="ek-app-header__brand">
                <h1 class="ek-app-header__title">
                    <span class="ek-app-header__app-name">{{ config('app.name', 'Faktury') }}</span>
                    @if ($view === 'home' && $activeProfile)
                        <span class="ek-app-header__meta">
                            <span class="ek-app-header__sep" aria-hidden="true">·</span>
                            <span class="ek-app-header__profile">{{ $activeProfile->name }}</span>
                        </span>
                    @else
                        <span class="ek-app-header__meta">
                            <span class="ek-app-header__sep" aria-hidden="true">·</span>
                            <span class="ek-app-header__profile">{{ __('app.shell.welcome_back', ['name' => auth()->user()->name]) }}</span>
                        </span>
                    @endif
                </h1>
            </div>

            <div class="ek-app-header__desktop">
                @if ($profiles->isNotEmpty())
                    <button
                        type="button"
                        wire:click="goHome"
                        class="ek-nav-link {{ $view === 'home' ? 'ek-nav-link--active' : '' }}"
                    >
                        {{ __('app.shell.nav.invoices') }}
                    </button>
                @endif

                <x-locale-toggle />
                <x-theme-toggle />

                @if ($profiles->isNotEmpty())
                    @if ($activeProfile && $view === 'home')
                        <button
                            type="button"
                            wire:click="goToEditProfile({{ $activeProfile->id }})"
                            class="ek-toggle"
                            title="{{ __('app.shell.nav.edit_company_profile') }}"
                            aria-label="{{ __('app.shell.nav.edit_company_profile') }}"
                        >
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    @endif

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            class="ek-app-profile-btn"
                        >
                            <span class="truncate">{{ $activeProfile?->name ?? __('app.shell.nav.select_company') }}</span>
                            <svg class="h-4 w-4 shrink-0 ek-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            class="ek-app-profile-menu"
                        >
                            @foreach ($profiles as $profile)
                                <button
                                    type="button"
                                    wire:click="switchProfile({{ $profile->id }})"
                                    @click="open = false"
                                    class="ek-app-profile-menu__item"
                                >
                                    <span class="truncate">{{ $profile->name }}</span>
                                    @if ($activeProfile?->id === $profile->id)
                                        <svg class="h-4 w-4 shrink-0" style="color: var(--primary);" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach

                            <div class="ek-app-profile-menu__footer">
                                <button
                                    type="button"
                                    wire:click="goToCreateProfile"
                                    @click="open = false"
                                    class="ek-app-profile-menu__add"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('app.shell.nav.add_company') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="goToSettings"
                    class="ek-toggle"
                    title="{{ __('app.shell.nav.settings') }}"
                    aria-label="{{ __('app.shell.nav.settings') }}"
                >
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>

                <button wire:click="logout" type="button" class="ek-btn-secondary ek-app-logout-btn">
                    {{ __('app.shell.nav.logout') }}
                </button>
            </div>

            <div class="ek-app-header__mobile">
                <x-locale-toggle />
                <x-theme-toggle />
                <button
                    type="button"
                    class="ek-toggle"
                    @click="mobileNavOpen = !mobileNavOpen"
                    :aria-expanded="mobileNavOpen"
                    aria-controls="app-mobile-nav"
                    aria-label="{{ __('app.shell.nav.menu') }}"
                >
                    <svg x-show="!mobileNavOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileNavOpen" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div
            id="app-mobile-nav"
            x-show="mobileNavOpen"
            x-cloak
            x-transition.opacity
            class="ek-app-mobile-nav"
            @click.outside="mobileNavOpen = false"
        >
            @if ($profiles->isNotEmpty())
                <button
                    type="button"
                    wire:click="goHome"
                    @click="mobileNavOpen = false"
                    class="ek-app-mobile-nav__link {{ $view === 'home' ? 'ek-app-mobile-nav__link--active' : '' }}"
                >
                    {{ __('app.shell.mobile_nav.invoices') }}
                </button>

                @if ($activeProfile && $view === 'home')
                    <button
                        type="button"
                        wire:click="goToEditProfile({{ $activeProfile->id }})"
                        @click="mobileNavOpen = false"
                        class="ek-app-mobile-nav__link"
                    >
                        {{ __('app.shell.mobile_nav.edit_company_profile') }}
                    </button>
                @endif

                <div class="ek-app-mobile-nav__section">
                    <p class="ek-app-mobile-nav__label">{{ __('app.shell.mobile_nav.company') }}</p>
                    @foreach ($profiles as $profile)
                        <button
                            type="button"
                            wire:click="switchProfile({{ $profile->id }})"
                            @click="mobileNavOpen = false"
                            class="ek-app-mobile-nav__link {{ $activeProfile?->id === $profile->id ? 'ek-app-mobile-nav__link--active' : '' }}"
                        >
                            {{ $profile->name }}
                        </button>
                    @endforeach
                    <button
                        type="button"
                        wire:click="goToCreateProfile"
                        @click="mobileNavOpen = false"
                        class="ek-app-mobile-nav__link ek-app-mobile-nav__link--accent"
                    >
                        {{ __('app.shell.mobile_nav.add_company') }}
                    </button>
                </div>
            @endif

            <button
                type="button"
                wire:click="goToSettings"
                @click="mobileNavOpen = false"
                class="ek-app-mobile-nav__link {{ $view === 'settings' ? 'ek-app-mobile-nav__link--active' : '' }}"
            >
                {{ __('app.shell.mobile_nav.settings') }}
            </button>

            <button
                type="button"
                wire:click="logout"
                @click="mobileNavOpen = false"
                class="ek-app-mobile-nav__link ek-app-mobile-nav__link--danger"
            >
                {{ __('app.shell.mobile_nav.logout') }}
            </button>
        </div>
    </header>

    <main @class([
        'ek-app-main',
        'ek-app-main--narrow' => $view !== 'home',
        'ek-app-main--home' => $view === 'home',
    ])>
        @if ($view === 'home')
            @include('livewire.invoices.dashboard')
        @elseif ($view === 'company-create' || ($view === 'company-edit' && $profile))
            @include('livewire.company-profile.form', ['taxpayerTypes' => $taxpayerTypes])
        @elseif ($view === 'settings')
            <livewire:user-settings wire:key="user-settings" />
        @endif
    </main>
</div>
