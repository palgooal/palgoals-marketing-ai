<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') | Palgoals Marketing AI</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans antialiased text-gray-900">
        <div class="min-h-screen lg:flex">
            <aside class="w-full bg-slate-900 text-slate-100 lg:min-h-screen lg:w-64 lg:shrink-0">
                <div class="border-b border-slate-800 px-6 py-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Internal Admin</p>
                    <p class="mt-2 text-sm text-slate-300">Navigation</p>
                </div>

                <nav class="space-y-1 px-3 py-6">
                    <a
                        href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Dashboard
                    </a>

                    <a
                        href="{{ route('brand.edit') }}"
                        class="{{ request()->routeIs('brand.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Brand Profile
                    </a>

                    <a
                        href="{{ route('services.index') }}"
                        class="{{ request()->routeIs('services.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Services
                    </a>

                    <a
                        href="{{ route('template-categories.index') }}"
                        class="{{ request()->routeIs('template-categories.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Template Categories
                    </a>

                    <a
                        href="{{ route('templates.index') }}"
                        class="{{ request()->routeIs('templates.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Templates
                    </a>

                    <a
                        href="{{ route('knowledge-documents.index') }}"
                        class="{{ request()->routeIs('knowledge-documents.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Knowledge Documents
                    </a>

                    <a
                        href="{{ route('prompts.index') }}"
                        class="{{ request()->routeIs('prompts.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Prompt Templates
                    </a>

                    <a
                        href="{{ route('settings.edit') }}"
                        class="{{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }} block rounded-lg px-3 py-2 text-sm font-medium transition"
                    >
                        Settings
                    </a>
                </nav>
            </aside>

            <div class="flex-1">
                <header class="border-b border-gray-200 bg-white">
                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Palgoals Marketing AI</h1>
                            <p class="mt-1 text-sm text-gray-500">@yield('page-title', 'Admin')</p>
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>{{ auth()->user()->name }}</span>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="font-medium text-gray-700 hover:text-gray-900">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="px-6 py-8 lg:px-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
