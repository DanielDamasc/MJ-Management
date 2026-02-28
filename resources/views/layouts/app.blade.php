<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name', 'MJ Management') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png?v=1') }}">

        {{-- SCRIPT DO DARK MODE PARA LIVEWIRE SPA --}}
        <script>
            function applyTheme() {
                if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }

            // Aplica no primeiro carregamento (Evita a tela piscar branco)
            applyTheme();

            // Reaplica silenciosamente toda vez que o wire:navigate trocar de página
            document.addEventListener('livewire:navigated', applyTheme);
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased h-screen overflow-hidden flex" x-data="{open: false}">

        {{-- Fundo escuro de quando a sidebar está aberta no mobile. --}}
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 bg-black/50 z-20 lg:hidden"
            @click="open = false">
        </div>

        <aside
            class="
                fixed inset-y-0 left-0 z-30 w-64 bg-primary-950 dark:bg-gray-900 text-white dark:text-gray-300
                transform transition-transform duration-200
                flex flex-col border-r border-transparent dark:border-gray-800
                lg:translate-x-0"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
            >

            <div class="h-16 px-4 flex items-center justify-between border-b border-primary-800 dark:border-gray-800 bg-primary-900 dark:bg-gray-900">
                <h1 class="text-xl font-bold text-white">MJ Management</h1>
                <button
                    class="p-2 focus:outline-none focus:bg-primary-800 dark:focus:bg-gray-800 hover:bg-primary-600 dark:hover:bg-gray-800 rounded-md lg:hidden text-primary-200 dark:text-gray-400 hover:text-white"
                    @click="open = false">
                    <x-heroicon-s-chevron-left class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

                {{-- NAVIGATE OPTIONS --}}
                @role('adm')
                    <a href="/" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('/') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-s-home class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Visão Geral</span>
                    </a>
                @endrole

                @role('adm')
                    <a href="/colaboradores" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('colaboradores') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-s-briefcase class="h-5 w-5 mr-2" />
                        <span class="font-semibold text-md">Colaboradores</span>
                    </a>
                @endrole

                @hasrole(['adm', 'assistente'])
                    <a href="/clientes" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('clientes') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-s-users class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Clientes</span>
                    </a>
                @endhasrole

                @hasrole(['adm', 'assistente'])
                    <a href="/ar-condicionados" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('ar-condicionados') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-ionicon-snow-outline class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Ar-condicionados</span>
                    </a>
                @endhasrole

                @hasrole(['adm', 'assistente'])
                    <a href="/servicos" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('servicos') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-s-clipboard-document-check class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Ordens de Serviço</span>
                    </a>
                @endhasrole

                @role('adm')
                    <a href="/pmoc" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('pmoc') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-m-table-cells class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">PMOC</span>
                    </a>
                @endrole

                @role('adm')
                    <a href="/logs" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('logs') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-heroicon-s-folder class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Logs</span>
                    </a>
                @endrole

                @role('executor')
                    <a href="/servicos-executor" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('servicos-executor') ? 'bg-secondary-700 dark:bg-secondary-600 text-white' : 'text-primary-200 dark:text-gray-400 hover:bg-primary-800 dark:hover:bg-gray-800 hover:text-white dark:hover:text-white' }}">
                        <x-ionicon-calendar-sharp class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Agendamentos</span>
                    </a>
                @endrole

            </nav>

            <div class="p-4 border-t border-primary-800 dark:border-gray-800 bg-primary-950 dark:bg-gray-900 shrink-0 space-y-1">

                <div class="flex items-center gap-3 px-4 py-2 mb-2 rounded-lg bg-primary-900/50 dark:bg-gray-800/50 border border-primary-800/50 dark:border-gray-700">
                    <div class="shrink-0">
                        <x-heroicon-o-user-circle class="w-8 h-8 text-primary-400 dark:text-gray-400" />
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs text-primary-400 dark:text-gray-400 font-medium">Olá,</span>
                        <span class="font-bold text-sm text-white truncate" title="{{ auth()->user()->name }}">
                            {{ auth()->user()->name }}
                        </span>
                    </div>

                    <button
                        x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                        @click="
                            document.documentElement.classList.toggle('dark');
                            isDark = document.documentElement.classList.contains('dark');
                            localStorage.setItem('darkMode', isDark);
                        "
                        x-on:livewire:navigated.window="isDark = document.documentElement.classList.contains('dark')"
                        class="ml-auto p-2 text-primary-400 hover:text-white hover:bg-primary-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-600"
                        title="Alternar Tema Escuro/Claro"
                    >
                        {{-- Lua (Aparece no modo Claro) --}}
                        <x-heroicon-o-moon x-show="!isDark" class="w-5 h-5" />

                        {{-- Sol (Aparece no modo Escuro) --}}
                        <x-heroicon-o-sun x-show="isDark" class="w-5 h-5" style="display: none;" />
                    </button>
                </div>

                <a href="{{ route('logout') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary-200 dark:text-gray-400 hover:bg-red-500/10 dark:hover:bg-red-500/20 hover:text-red-400 transition-colors group">
                    <x-ionicon-exit-outline class="w-5 h-5 group-hover:text-red-400 transition-colors" />
                    <span class="font-semibold text-md">Sair</span>
                </a>
            </div>

        </aside>

        <div class="flex-1 flex flex-col h-full overflow-hidden lg:ml-64">
            <header class="flex bg-primary-900 text-white items-center justify-between p-1 lg:hidden">
                <div class="flex flex-row items-center gap-2">
                    <button
                        class="p-2 focus:outline-none focus:bg-primary-800 hover:bg-primary-600 rounded-md"
                        @click="open = true">
                        <x-ionicon-menu-outline class="w-8 h-8"/>
                    </button>
                    <span class="block text-xl font-bold">MJ Management</span>
                </div>
                <div class="mr-4">
                    <a href="{{ route('logout') }}"
                    class="flex items-center gap-1 rounded-lg text-primary-200 p-2 active:bg-primary-800">
                        <x-ionicon-exit-outline class="w-6 h-6" />
                    </a>
                </div>
            </header>

            <main class="p-6 overflow-y-auto flex-1">

                {{ $slot }}

            </main>

        </div>

        <x-notification />
    </body>
    {{--
        Usando um truque sujo para forçar o tailwind a gerar essas classes:
        <div class="bg-green-100 text-green-800"></div>
        <div class="bg-blue-100 text-blue-800"></div>
        <div class="bg-red-100 text-red-800"></div>
    --}}
</html>
