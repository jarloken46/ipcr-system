<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isIpcr = ($submissionType ?? 'ipcr') === 'ipcr';
        $pageTitle = $isIpcr ? 'IPCR Submission' : 'OPCR Submission';
    @endphp
    <title>{{ $pageTitle }} - IPCR Dashboard</title>
    <link rel="icon" type="image/jpeg" href="{{ \App\Support\MediaAsset::publicImageUrl('urs_logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/dashboard_faculty_index.css', 'resources/css/dashboard_faculty_my-ipcrs.css', 'resources/js/dashboard_faculty_index.js'])
</head>
<body class="bg-gray-50" style="visibility: hidden;">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <img src="{{ \App\Support\MediaAsset::publicImageUrl('urs_logo.jpg') }}" alt="URS Logo" class="h-10 sm:h-12 w-auto object-contain flex-shrink-0">
                    <h1 class="text-base sm:text-xl font-bold text-gray-900">IPCR Dashboard</h1>
                </div>

                <div class="hidden lg:flex items-center space-x-6 xl:space-x-8">
                    <a href="{{ route('faculty.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    @if(auth()->user()->hasRole('director'))
                        <a href="{{ route('director.monitoring') }}" class="text-blue-600 font-semibold hover:text-blue-700">Monitoring</a>
                    @endif
                    <a href="{{ route('faculty.my-ipcrs') }}" class="text-gray-600 hover:text-gray-900">My IPCRs</a>
                    @if(auth()->user()->hasRole('hr'))
                        <a href="{{ route('faculty.summary-reports') }}" class="text-gray-600 hover:text-gray-900">Summary Reports</a>
                    @endif
                    <a href="{{ route('faculty.profile') }}" class="text-gray-600 hover:text-gray-900">Profile</a>

                    <div class="flex items-center space-x-3">
                        @if(auth()->user()->hasProfilePhoto())
                            <img src="{{ auth()->user()->profile_photo_url }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="profile-img">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff"
                                 alt="{{ auth()->user()->name }}"
                                 class="profile-img">
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">Logout</button>
                    </form>
                </div>

                <div class="flex lg:hidden items-center space-x-3">
                    <div class="flex items-center">
                        @if(auth()->user()->hasProfilePhoto())
                            <img src="{{ auth()->user()->profile_photo_url }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="profile-img">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff"
                                 alt="{{ auth()->user()->name }}"
                                 class="profile-img">
                        @endif
                    </div>
                    <div class="hamburger" onclick="toggleMobileMenu()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>

            <div class="mobile-menu-overlay lg:hidden" onclick="toggleMobileMenu()"></div>

            <div class="mobile-menu lg:hidden flex-col space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Menu</h2>
                    <button onclick="toggleMobileMenu()" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <a href="{{ route('faculty.dashboard') }}" class="block text-gray-600 hover:text-gray-900 py-2">Dashboard</a>
                @if(auth()->user()->hasRole('director'))
                    <a href="{{ route('director.monitoring') }}" class="block text-blue-600 font-semibold hover:text-blue-700 py-2">Monitoring</a>
                @endif
                <a href="{{ route('faculty.my-ipcrs') }}" class="block text-gray-600 hover:text-gray-900 py-2">My IPCRs</a>
                @if(auth()->user()->hasRole('hr'))
                    <a href="{{ route('faculty.summary-reports') }}" class="block text-gray-600 hover:text-gray-900 py-2">Summary Reports</a>
                @endif
                <a href="{{ route('faculty.profile') }}" class="block text-gray-600 hover:text-gray-900 py-2">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-700 font-semibold py-2">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $pageTitle }}</h2>
                <p class="text-sm text-gray-500 mt-1">Read-only preview for monitoring.</p>
            </div>
            <a href="{{ route('director.monitoring') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Monitoring
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-300">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm sm:text-lg font-bold text-gray-900 mb-2">{{ $submission->title }}</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Year: <span class="font-semibold">{{ $submission->school_year ?? 'N/A' }}</span></p>
                        <p class="text-xs sm:text-sm text-gray-600">Period: <span class="font-semibold">{{ $submission->semester ?? 'N/A' }}</span></p>
                        <p class="text-xs text-gray-500 mt-1">Submitted {{ $submission->submitted_at?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="text-xs text-gray-600">
                        <div class="font-semibold text-gray-900">{{ $submission->user?->name ?? 'Unknown' }}</div>
                        <div>{{ $submission->user?->employee_id ?? 'N/A' }}</div>
                        <div>{{ $submission->user?->department?->code ?? $submission->user?->department?->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 text-xs sm:text-sm mt-3">
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Ratee:</span>
                        <span class="font-semibold text-gray-900 truncate">{{ $submission->user?->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Approved By:</span>
                        <span class="font-semibold text-gray-900">{{ $submission->approved_by ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Noted By:</span>
                        <span class="font-semibold text-gray-900">{{ $submission->noted_by ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto px-2 sm:px-6 py-3 sm:py-4">
                <table id="submissionPreviewTable" class="w-full border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700" rowspan="2" style="width: 15%;">MFO</th>
                            <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700" rowspan="2" style="width: 25%;">Success Indicators<br><span class="font-semibold text-gray-500">(Target + Measures)</span></th>
                            <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700" rowspan="2" style="width: 20%;">Actual Accomplishments</th>
                            <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700" colspan="4">Rating</th>
                            <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700" rowspan="2" style="width: 15%;">Remarks</th>
                        </tr>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-600" style="width: 8%;">Q</th>
                            <th class="border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-600" style="width: 8%;">E</th>
                            <th class="border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-600" style="width: 8%;">T</th>
                            <th class="border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-600" style="width: 8%;">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        {!! $submission->table_body_html !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.body) {
                document.body.style.visibility = 'visible';
            }
            const previewTable = document.getElementById('submissionPreviewTable');
            if (previewTable) {
                previewTable.querySelectorAll('input, textarea, select').forEach(function (el) {
                    if (el.tagName === 'SELECT') {
                        el.disabled = true;
                    } else {
                        el.readOnly = true;
                    }
                    el.setAttribute('tabindex', '-1');
                    el.style.pointerEvents = 'none';
                });
            }
        });
    </script>
</body>
</html>
