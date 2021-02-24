@php
use App\Models\Slot;
use App\Models\Subject;
$name = Route::currentRouteName();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Dennis Eum">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <title>tfa-calendar</title>

    @yield('styles')
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="bg-gray-800 fixed w-full z-20" x-data="{ view: false }">
        <form class="hidden" id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a class="text-white text-xl font-black tracking-wide p-3 bg-gray-600 rounded-md select-none" href="{{ route('dashboard') }}">tfa-calendar</a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('dashboard') }}" class="nav-link @if ($name == 'dashboard') active @else inactive @endif">Dashboard</a>
                            @if (Auth::user()->role->name == 'admin')
                            <a href="{{ route('admin.users') }}" class="nav-link @if ($name == 'admin.users') active @else inactive @endif">Users</a>
                            <a href="{{ route('admin.reports') }}" class="nav-link @if ($name == 'admin.reports') active @else inactive @endif">Reports</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        @if (Auth::user()->role->name == 'student' && $name == 'dashboard')
                        <div class="space-y-1 mr-4 w-48" x-data="Components.customSelect({ open: false, value: 1, selected: 1 })" x-init="init()">
                            <div class="relative">
                                <span class="inline-block w-full rounded-md shadow-sm">
                                    <button x-ref="button" @click="onButtonClick()" type="button" aria-haspopup="listbox" :aria-expanded="open" aria-labelledby="assigned-to-label" class="cursor-default relative w-full rounded-md border border-gray-300 bg-white pl-3 pr-10 py-2 text-left focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                        <div class="flex items-center space-x-3">
                                            <span x-text="['All subjects'@foreach(Subject::get() as $subject),'{{ $subject->name }}'@endforeach][value - 1]" class="block truncate">All subjects</span>
                                        </div>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </span>
                                <div x-show="open" @focusout="onEscape()" @click.away="open = false" x-description="Select popover, show/hide based on select state." x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute mt-1 w-full rounded-md bg-white shadow-lg" style="display: none;">
                                    <ul @keydown.enter.stop.prevent="onOptionSelect()" @keydown.space.stop.prevent="onOptionSelect()" @keydown.escape="onEscape()" @keydown.arrow-up.prevent="onArrowUp()" @keydown.arrow-down.prevent="onArrowDown()" x-ref="listbox" tabindex="-1" role="listbox" aria-labelledby="assigned-to-label" :aria-activedescendant="activeDescendant" class="max-h-56 rounded-md py-1 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
                                        <li id="assigned-to-option-1" role="option" @click="choose(1)" @mouseenter="selected = 1" @mouseleave="selected = null" :class="{ 'text-white bg-gray-600': selected === 1, 'text-gray-900': !(selected === 1) }" class="text-gray-900 cursor-default select-none relative py-2 pl-4 pr-9">
                                            <div class="flex items-center space-x-3">
                                                <span :class="{ 'font-semibold': value === 1, 'font-normal': !(value === 1) }" class="font-normal block truncate">
                                                    All subjects
                                                </span>
                                            </div>
                                            <span x-show="value === 1" :class="{ 'text-white': selected === 1, 'text-gray-600': !(selected === 1) }" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-600" style="display: none;">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </li>
                                        @foreach(Subject::get() as $subject)
                                        <li id="assigned-to-option-{{ $subject->id +1 }}" role="option" @click="choose({{ $subject->id + 1 }})" @mouseenter="selected = {{ $subject->id + 1 }}" @mouseleave="selected = null" aria-selected="true" :class="{ 'text-white bg-gray-600': selected === {{ $subject->id + 1 }}, 'text-gray-900': !(selected === {{ $subject->id + 1 }}) }" class="text-gray-900 cursor-default select-none relative py-2 pl-4 pr-9">
                                            <div class="flex items-center space-x-3">
                                                <span :class="{ 'font-semibold': value === {{ $subject->id + 1 }}, 'font-normal': !(value === {{ $subject->id + 1 }}) }" class="font-normal block truncate">
                                                    {{ $subject->name }}
                                                </span>
                                            </div>
                                            <span x-show="value === {{ $subject->id + 1 }}" :class="{ 'text-white': selected === {{ $subject->id +1 }}, 'text-gray-600': !(selected === {{ $subject->id + 1 }}) }" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-600" style="display: none;">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if (in_array($name, ['dashboard', 'admin.users', 'admin.probations', 'admin.reports']))
                        <div class="bg-gray-700 text-white px-3 py-2 rounded-md text-base font-medium mr-3" id="processingStatus">
                            <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" name="done" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg class="animate-spin h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" name="load">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        @endif
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button class="max-w-xs bg-gray-800 rounded-full flex items-center text-sm focus:outline-none" id="user-menu" aria-haspopup="true" @click="open = !open">
                                    <span class="sr-only">Open user menu</span>
                                    <svg class="h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div class="origin-top-right absolute right-0 mt-6 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10 hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu" @click.away="open = false" x-bind:class="{'hidden':!open, 'block': open}" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95">
                                @if ($name != 'settings')
                                <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                                @endif
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" @click="document.getElementById('logout-form').submit()">Log out</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none" @click="view = !view">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" x-bind:class="{ 'hidden': view, 'block': !view }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" x-bind:class="{ 'hidden': !view, 'block': view }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="md:hidden" x-bind:class="{ 'hidden': !view, 'block': view }" x-cloak>
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('dashboard') }}" class="nav-link block @if ($name == 'dashboard') active @else inactive @endif">Dashboard</a>
                @if (Auth::user()->role->name == 'admin')
                <a href="{{ route('admin.users') }}" class="nav-link block @if ($name == 'admin.users') active @else inactive @endif">Users</a>
                <a href="{{ route('admin.reports') }}" class="nav-link block @if ($name == 'admin.reports') active @else inactive @endif">Reports</a>
                @endif
            </div>
            <div class="pt-4 pb-3 border-t border-gray-700">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium leading-none text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="{{ route('settings') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">Settings</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700" @click="document.getElementById('logout-form').submit()">Log out</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex flex-col h-screen justify-between">
        <main class="mt-14 @if ($name != 'dashboard') h-full @endif">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="container-app">
                    <div id="alertContainer" x-data="{open: @if (app('request')->input('alert') == null) false @else true @endif  }" x-cloak>
                        <div id="alertBox" class="absolute top-0 left-1/2 z-10 max-w-lg w-full mt-20 text-white px-6 py-4 border-0 rounded-lg shadow-lg mb-4 bg-gray-700 select-none" style="transform:translateX(-50%);" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <span class="inline-block align-middle mr-8 pointer-events-none" id="alertText">
                                {{app('request')->input('alert')}}
                            </span>
                            <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none cursor-pointer" @click="open=!open">
                                <span>×</span>
                            </button>
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </main>
        <footer class="mt-4 pb-3">
            <p class="text-center text-gray-400">© <span id="year"></span> Dennis Eum, TFA</p>
        </footer>
    </div>
    <div id="reportContainer" x-data="{open:false, reportForm:'reportBugForm'}" x-cloak>
        <button class="fixed bottom-5 z-10 right-5 text-white bg-gray-700 hover:bg-gray-600 hover:text-gray-200 rounded-full focus:outline-none select-none w-12 h-12 flex items-center justify-center shadow-lg" @click="open=!open">
            <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
        <div class="fixed inset-0 overflow-y-auto z-30" x-show="open">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true" x-show="open">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                <div @click.away="open=!open" x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Report
                                </h3>
                                <div class="mt-2">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="reportType" disabled>
                                            <option value="1">Technical problem/bug report</option>
                                            @if (Auth::user()->role->name == 'tutor')
                                            <option value="2">Absent student report</option>
                                            @elseif (Auth::user()->role->name == 'student')
                                            <option value="2">Absent tutor report</option>
                                            @endif
                                        </select>
                                        <label>Type of report</label>
                                    </div>
                                    <form id="reportBugForm" novalidate>
                                        <div class="form-floating">
                                            <textarea class="form-element" name="message" required></textarea>
                                            <label>Briefly describe the report</label>
                                            <small class="text-help">
                                                Limit 1000 characters.
                                            </small>
                                        </div>
                                    </form>
                                    <form id="reportPersonForm" style="display:none;" novalidate>
                                        <div class="form-floating">
                                            <select class="form-select" name="start" required>
                                            </select>
                                            <label>Which slot today was the person missing?</label>
                                            <small class="text-help">
                                                Only report if the person wasn't there more than 10 minutes after the session starts.
                                            </small>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="btn-positive btn-modal sm:ml-3" @click="document.getElementById(reportForm).requestSubmit()">
                            Report
                        </button>
                        <button type="button" class="mt-3 btn-neutral btn-modal sm:mt-0" @click="open=!open">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const reportEnabled = true;
    </script>
    @if (Auth::user()->role->name == 'tutor')
    <script>
        const accountType = 'tutor';
    </script>
    @elseif (Auth::user()->role->name == 'student')
    <script>
        const accountType = 'student';
    </script>
    @elseif (Auth::user()->role->name == 'admin')
    <script>
        const accountType = 'admin';
    </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/luxon@1.26.0/build/global/luxon.min.js" integrity="sha256-4sbTzmCCW9LGrIh5OsN8V5Pfdad1F1MwhLAOyXKnsE0=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/config.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>