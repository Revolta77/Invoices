@php
    $currentLocale = app()->getLocale();
    $locales = config('locales.available', []);
@endphp

@if (count($locales) > 1)
    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button
            type="button"
            class="ek-toggle ek-locale-toggle"
            @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="listbox"
            aria-label="{{ __('app.switch_language') }}"
        >
            @php($current = $locales[$currentLocale] ?? null)
            @if ($current && ! empty($current['flag_icon']))
                <img
                    src="{{ asset($current['flag_icon']) }}"
                    alt=""
                    class="ek-locale-flag"
                    width="20"
                    height="20"
                >
            @else
                <span class="ek-locale-flag ek-locale-flag--emoji" aria-hidden="true">{{ $current['flag'] ?? '🌐' }}</span>
            @endif
        </button>

        <div x-show="open" x-cloak class="ek-locale-menu" role="listbox">
            @foreach ($locales as $code => $locale)
                <a
                    href="{{ route('locale.switch', $code) }}"
                    @class([
                        'ek-locale-menu__item',
                        'ek-locale-menu__item--active' => $currentLocale === $code,
                    ])
                    @click="open = false"
                >
                    @if (! empty($locale['flag_icon']))
                        <img
                            src="{{ asset($locale['flag_icon']) }}"
                            alt=""
                            class="ek-locale-flag"
                            width="20"
                            height="20"
                        >
                    @else
                        <span class="ek-locale-flag ek-locale-flag--emoji" aria-hidden="true">{{ $locale['flag'] }}</span>
                    @endif
                    <span>{{ $locale['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
