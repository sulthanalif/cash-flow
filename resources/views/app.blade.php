<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        @php
            // Ambil data Appearance sekali saja biar efisien
            $appearanceData = \App\Models\Appearance::latest()->first();
            
            // Logika Name
            $appName = $appearanceData?->name_app ?? config('app.name', 'Laravel');
            
            // Logika Icon (PENTING: Gunakan Storage::url agar path 'public/icons/...' bisa dibaca browser)
            $iconPath = $appearanceData?->icon_app;
            $appIcon = $iconPath ? \Illuminate\Support\Facades\Storage::url($iconPath) : '/favicon.ico';
        @endphp

        <title inertia>{{  $appName }}</title>

        <script>
            window.APP_NAME = "{{ $appName }}";
        </script>
        <link rel="icon" href="{{ $appIcon }}" sizes="any">
        <link rel="icon" href="{{ $appIcon }}"> 
        <link rel="apple-touch-icon" href="{{ $appIcon }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>