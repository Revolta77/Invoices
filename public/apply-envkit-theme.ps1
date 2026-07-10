# Spusti v PowerShell ako Administrator:
#   Set-ExecutionPolicy Bypass -Scope Process -Force
#   & "C:\ProgramData\envkit\projects\faktury\public\apply-envkit-theme.ps1"

$project = "C:\ProgramData\envkit\projects\faktury"
$resources = Join-Path $project "resources"

takeown /F $resources /R /D Y | Out-Null
icacls $resources /grant "${env:USERNAME}:(OI)(CI)F" /T | Out-Null

$appCss = @'
@import 'tailwindcss';

@custom-variant dark (&:where(.dark, .dark *));

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../views/**/*.blade.php';

:root {
    --radius: 0.625rem;
    --background: #eef4f8;
    --foreground: #0f1729;
    --card: #ffffff;
    --card-foreground: #0f1729;
    --popover: #ffffff;
    --popover-foreground: #0f1729;
    --primary: #0d9488;
    --primary-foreground: #ffffff;
    --secondary: #f2f7f8;
    --secondary-foreground: #0f1729;
    --muted: #f2f7f8;
    --muted-foreground: #5b6b7d;
    --accent: #2563eb;
    --accent-foreground: #ffffff;
    --destructive: #dc2626;
    --destructive-foreground: #ffffff;
    --border: #0f172a1a;
    --input: #0f172a2e;
    --ring: #0d9488;
    --surface: #ffffff;
    --surface-raised: #e5eef2;
    --text-secondary: #405166;
    --text-muted: #5b6b7d;
    --success: #059669;
    --warning: #d97706;
    --danger: #dc2626;
}

.dark {
    --background: #06090d;
    --foreground: #edf5fb;
    --card: #101821;
    --card-foreground: #edf5fb;
    --popover: #0a0f15;
    --popover-foreground: #edf5fb;
    --primary: #2dd4aa;
    --primary-foreground: #04130d;
    --secondary: #1a2532;
    --secondary-foreground: #edf5fb;
    --muted: #101821;
    --muted-foreground: #afbdca;
    --accent: #60a5fa;
    --accent-foreground: #04111f;
    --destructive: #f87171;
    --destructive-foreground: #1a0707;
    --border: #a6b7cc24;
    --input: #b8c8dc47;
    --ring: #2dd4aa;
    --surface: #101821;
    --surface-raised: #1a2532;
    --text-secondary: #afbdca;
    --text-muted: #8595a6;
    --success: #34d399;
    --warning: #fbbf24;
    --danger: #f87171;
}

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
    --radius-lg: var(--radius);
    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-card: var(--card);
    --color-card-foreground: var(--card-foreground);
    --color-primary: var(--primary);
    --color-primary-foreground: var(--primary-foreground);
    --color-accent: var(--accent);
    --color-border: var(--border);
    --color-input: var(--input);
    --color-ring: var(--ring);
    --color-surface: var(--surface);
    --color-surface-raised: var(--surface-raised);
    --color-text-secondary: var(--text-secondary);
    --color-text-muted: var(--text-muted);
    --color-success: var(--success);
    --color-warning: var(--warning);
    --color-danger: var(--danger);
    --color-muted: var(--muted);
    --color-muted-foreground: var(--muted-foreground);
}

@layer base {
    * {
        border-color: var(--border);
    }

    body {
        @apply bg-background text-foreground antialiased;
        transition: background-color 0.2s, color 0.2s;
    }

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: color-mix(in srgb, var(--text-secondary) 32%, transparent);
        border-radius: 8px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: color-mix(in srgb, var(--text-secondary) 55%, transparent);
    }
}
'@

$appJs = @'
function getStoredTheme() {
    return localStorage.getItem('theme');
}

function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
}

function initTheme() {
    const theme = getStoredTheme() ?? getSystemTheme();
    applyTheme(theme);
}

function toggleTheme() {
    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem('theme', next);
    applyTheme(next);
}

initTheme();

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');
    if (toggle) {
        toggleTheme();
    }
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
    if (!getStoredTheme()) {
        applyTheme(event.matches ? 'dark' : 'light');
    }
});
'@

$themeScript = @'
        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

'@

Set-Content -Path (Join-Path $project "resources\css\app.css") -Value $appCss -Encoding UTF8
Set-Content -Path (Join-Path $project "resources\js\app.js") -Value $appJs -Encoding UTF8

# Layout app.blade.php
$appLayout = Get-Content (Join-Path $project "resources\views\layouts\app.blade.php") -Raw
$appLayout = $appLayout -replace '<title>\{\{ \$title \?\? config\(''app\.name''\) \}\}</title>\s+', "<title>{{ `$title ?? config('app.name') }}</title>`n`n$themeScript"
$appLayout = $appLayout -replace 'class="min-h-screen bg-slate-50 text-slate-900 antialiased"', 'class="min-h-screen"'
Set-Content (Join-Path $project "resources\views\layouts\app.blade.php") -Value $appLayout -Encoding UTF8

# Layout guest.blade.php
$guestLayout = @'
<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        @livewireStyles
    </head>
    <body class="min-h-screen">
        <div class="relative flex min-h-screen items-center justify-center px-4 py-12">
            <div class="absolute end-4 top-4">
                <x-theme-toggle />
            </div>

            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="bg-gradient-to-r from-primary to-accent bg-clip-text text-2xl font-semibold tracking-tight text-transparent">
                        {{ config('app.name', 'Faktury') }}
                    </h1>
                    <p class="mt-2 text-sm text-text-muted">Správa faktúr jednoducho a prehľadne</p>
                </div>

                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
'@
Set-Content (Join-Path $project "resources\views\layouts\guest.blade.php") -Value $guestLayout -Encoding UTF8

# Replace slate/indigo classes in views
$replacements = @{
    'border-slate-200 bg-white' = 'border-border bg-surface'
    'text-slate-600' = 'text-text-muted'
    'text-slate-700' = 'text-text-secondary'
    'text-slate-500' = 'text-text-muted'
    'border-slate-300' = 'border-input'
    'border-slate-200' = 'border-border'
    'bg-white' = 'bg-surface'
    'bg-slate-100' = 'bg-surface-raised'
    'bg-slate-50' = 'bg-surface-raised'
    'bg-indigo-600' = 'bg-primary'
    'hover:bg-indigo-700' = 'hover:bg-primary/90'
    'text-indigo-600' = 'text-primary'
    'hover:text-indigo-500' = 'hover:text-primary/80'
    'focus:border-indigo-500' = 'focus:border-primary'
    'focus:ring-indigo-500' = 'focus:ring-primary'
    'text-indigo-600 focus:ring-indigo-500' = 'text-primary focus:ring-primary'
    'text-red-600' = 'text-danger'
    'border-amber-200 bg-amber-50' = 'border-warning/30 bg-warning/10'
    'text-amber-900' = 'text-warning'
}

$viewFiles = @(
    "resources\views\livewire\auth\login.blade.php",
    "resources\views\livewire\auth\register.blade.php",
    "resources\views\livewire\dashboard.blade.php"
)

foreach ($file in $viewFiles) {
    $path = Join-Path $project $file
    $content = Get-Content $path -Raw
    foreach ($key in $replacements.Keys) {
        $content = $content.Replace($key, $replacements[$key])
    }
    Set-Content $path -Value $content -Encoding UTF8
}

# Dashboard header theme toggle
$dashboard = Get-Content (Join-Path $project "resources\views\livewire\dashboard.blade.php") -Raw
$dashboard = $dashboard -replace '(<div class="flex items-center gap-4">)', "`$1`n                <x-theme-toggle />"
Set-Content (Join-Path $project "resources\views\livewire\dashboard.blade.php") -Value $dashboard -Encoding UTF8

Write-Host "EnvKit tema bola aplikovana. Spusti: npm run build" -ForegroundColor Green
