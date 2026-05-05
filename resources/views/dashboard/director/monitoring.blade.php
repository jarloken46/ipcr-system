<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring - IPCR Dashboard</title>
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
                    <div class="relative">
                        <button onclick="toggleNotificationPopup()" class="text-gray-600 hover:text-gray-900 relative flex items-center gap-1">
                            Notifications
                            @if(($unreadCount ?? 0) > 0)
                                <span class="notification-badge" id="notifBadge" style="position: static; margin-left: 4px;">{{ $unreadCount }}</span>
                            @else
                                <span class="notification-badge hidden" id="notifBadge" style="position: static; margin-left: 4px;">0</span>
                            @endif
                        </button>

                        <div id="notificationPopup" class="notification-popup">
                            <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                <div class="flex items-center gap-2">
                                    <button onclick="markAllNotificationsRead()" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 transition-colors" title="Mark all as read">
                                        Mark all as read
                                    </button>
                                    <button onclick="toggleCompactMode()" class="compact-toggle-btn text-[10px] font-semibold px-2 py-0.5 rounded-full border transition-colors" title="Toggle compact view">
                                        <span class="compact-label">Compact</span>
                                    </button>
                                </div>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                <div class="p-2.5 notif-list">
                                    @forelse(($notifications ?? collect()) as $notif)
                                        @php
                                            $notifStyles = [
                                                'info' => 'notification-blue',
                                                'warning' => 'notification-yellow',
                                                'success' => 'notification-green',
                                                'danger' => 'notification-red',
                                            ];
                                            $iconColors = [
                                                'info' => 'text-blue-500',
                                                'warning' => 'text-yellow-600',
                                                'success' => 'text-green-500',
                                                'danger' => 'text-red-500',
                                            ];
                                            $isUnread = !in_array($notif->id, $readNotifIds ?? []);
                                        @endphp
                                        <div class="notification-item notif-card {{ $notifStyles[$notif->type] ?? 'notification-gray' }} mb-1.5{{ $isUnread ? ' notif-unread' : '' }}" data-notif-id="{{ $notif->id }}">
                                            <div class="flex items-start space-x-2">
                                                <svg class="w-3.5 h-3.5 {{ $iconColors[$notif->type] ?? 'text-gray-600' }} mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    @if($notif->type === 'success')
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    @elseif($notif->type === 'warning' || $notif->type === 'danger')
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    @else
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    @endif
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <p class="notif-title text-xs font-semibold text-gray-900">{{ $notif->title }}</p>
                                                        @if($isUnread)
                                                            <span class="notif-unread-dot w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                                        @endif
                                                    </div>
                                                    <p class="notif-message text-[11px] text-gray-600 mt-0.5">{{ Str::limit($notif->message, 80) }}</p>
                                                    <p class="notif-time text-[9px] text-gray-400 mt-0.5">{{ ($notif->published_at ?? $notif->created_at)->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="notification-item notification-gray">
                                            <div class="flex items-start space-x-2">
                                                <svg class="w-3.5 h-3.5 text-gray-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-900">No notifications</p>
                                                    <p class="text-[11px] text-gray-600">You're all caught up!</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="relative">
                        <button onclick="toggleNotificationPopup()" class="text-gray-600 hover:text-gray-900 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if(($unreadCount ?? 0) > 0)
                                <span class="notification-badge" id="notifBadgeMobile">{{ $unreadCount }}</span>
                            @else
                                <span class="notification-badge hidden" id="notifBadgeMobile">0</span>
                            @endif
                        </button>
                    </div>

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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Monitoring</h2>
                <p class="text-sm text-gray-500 mt-1">Campus-wide IPCR and OPCR submissions by department.</p>
            </div>
            @if(!$directorDepartmentId)
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Department not set
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Your department: {{ auth()->user()->department?->code ?? auth()->user()->department?->name ?? 'N/A' }}
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Department Overview</h3>
                <p class="text-xs text-gray-500 mt-1">Deans and submission activity across departments.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-500 bg-gray-50">
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Dean</th>
                            <th class="px-4 py-3">IPCR</th>
                            <th class="px-4 py-3">OPCR</th>
                            <th class="px-4 py-3">Latest IPCR</th>
                            <th class="px-4 py-3">Latest OPCR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($departments as $department)
                            @php
                                $deans = $deansByDepartment->get($department->id, collect());
                                $ipcrs = $ipcrByDepartment->get($department->id, collect());
                                $opcrs = $opcrByDepartment->get($department->id, collect());
                                $latestIpcr = $ipcrs->first();
                                $latestOpcr = $opcrs->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $department->code ?? $department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $department->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($deans->count() > 0)
                                        <div class="text-gray-900 font-semibold text-sm">
                                            {{ $deans->pluck('name')->implode(', ') }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        {{ $ipcrs->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        {{ $opcrs->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @if($latestIpcr)
                                        <div class="font-semibold text-gray-900">{{ $latestIpcr->user?->name ?? 'Unknown' }}</div>
                                        <div>{{ $latestIpcr->submitted_at?->format('M d, Y') ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @if($latestOpcr)
                                        <div class="font-semibold text-gray-900">{{ $latestOpcr->user?->name ?? 'Unknown' }}</div>
                                        <div>{{ $latestOpcr->submitted_at?->format('M d, Y') ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Faculty IPCR Submissions</h3>
                <p class="text-xs text-gray-500 mt-1">Submitted IPCRs from faculty members in every department.</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($departments as $department)
                    @php
                        $facultyIpcrs = $facultyIpcrByDepartment->get($department->id, collect());
                    @endphp
                    <details class="group">
                        <summary class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $department->code ?? $department->name }}</p>
                                <p class="text-xs text-gray-500">{{ $department->name }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ $facultyIpcrs->count() }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </summary>
                        <div class="px-6 pb-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs uppercase tracking-wide text-gray-500 bg-gray-50">
                                            <th class="px-3 py-2">Title</th>
                                            <th class="px-3 py-2">Faculty</th>
                                            <th class="px-3 py-2">Submitted</th>
                                            <th class="px-3 py-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($facultyIpcrs as $submission)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900 font-semibold">{{ $submission->title }}</td>
                                                <td class="px-3 py-2 text-xs text-gray-600">
                                                    <div class="font-semibold text-gray-900">{{ $submission->user?->name ?? 'Unknown' }}</div>
                                                    <div>{{ $submission->user?->employee_id ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-600">{{ $submission->submitted_at?->format('M d, Y') ?? 'N/A' }}</td>
                                                <td class="px-3 py-2">
                                                      <button type="button" onclick="openMonitoringIpcrModal({{ $submission->id }})" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">View</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-xs text-gray-500">No submitted IPCRs found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="px-6 py-6 text-center text-sm text-gray-500">No departments found.</div>
                @endforelse
            </div>
        </div>

        @if($directorDepartmentId)
            @php
                $ownIpcrs = $ipcrByDepartment->get($directorDepartmentId, collect());
                $ownOpcrs = $opcrByDepartment->get($directorDepartmentId, collect());
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Your Department Submissions</h3>
                    <p class="text-xs text-gray-500 mt-1">View submissions from your department.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">IPCR Submissions</h4>
                            <span class="text-xs text-gray-500">{{ $ownIpcrs->count() }} total</span>
                        </div>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 bg-gray-50">
                                        <th class="px-3 py-2">Title</th>
                                        <th class="px-3 py-2">Employee</th>
                                        <th class="px-3 py-2">Submitted</th>
                                        <th class="px-3 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($ownIpcrs as $submission)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900 font-semibold">{{ $submission->title }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-600">
                                                <div class="font-semibold text-gray-900">{{ $submission->user?->name ?? 'Unknown' }}</div>
                                                <div>{{ $submission->user?->employee_id ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-3 py-2 text-xs text-gray-600">{{ $submission->submitted_at?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="px-3 py-2">
                                                  <button type="button" onclick="openMonitoringIpcrModal({{ $submission->id }})" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">View</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-xs text-gray-500">No IPCR submissions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">OPCR Submissions</h4>
                            <span class="text-xs text-gray-500">{{ $ownOpcrs->count() }} total</span>
                        </div>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 bg-gray-50">
                                        <th class="px-3 py-2">Title</th>
                                        <th class="px-3 py-2">Employee</th>
                                        <th class="px-3 py-2">Submitted</th>
                                        <th class="px-3 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($ownOpcrs as $submission)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900 font-semibold">{{ $submission->title }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-600">
                                                <div class="font-semibold text-gray-900">{{ $submission->user?->name ?? 'Unknown' }}</div>
                                                <div>{{ $submission->user?->employee_id ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-3 py-2 text-xs text-gray-600">{{ $submission->submitted_at?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="px-3 py-2">
                                                <a href="{{ route('director.monitoring.opcr.show', $submission) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-semibold">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-xs text-gray-500">No OPCR submissions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-6 py-4 text-sm text-amber-700">
                Assign a department to this director to view department submissions.
            </div>
        @endif
    </div>
    <div id="monitoringIpcrDocumentContainer" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeMonitoringIpcrModal()"></div>
        <div class="relative mx-auto mt-2 sm:mt-8 mb-2 sm:mb-8 w-full max-w-6xl bg-white rounded-2xl shadow-lg max-h-[98vh] sm:max-h-[90vh] overflow-y-auto px-2 sm:px-0">
            <div class="bg-gray-50 px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-300 sticky top-0 bg-white z-10">
                <div class="flex justify-between items-start mb-3 sm:mb-4">
                    <div class="flex-1 min-w-0">
                        <input type="text" id="monitoringIpcrTitle" class="text-sm sm:text-lg font-bold text-gray-900 border-0 border-b-2 border-transparent bg-transparent px-1 sm:px-2 py-1 -ml-1 sm:-ml-2 w-full" value="" readonly>
                        <p class="text-xs sm:text-sm text-gray-600">Year: <span id="monitoringDisplaySchoolYear" class="font-semibold"></span></p>
                        <p class="text-xs sm:text-sm text-gray-600">Period: <span id="monitoringDisplaySemester" class="font-semibold"></span></p>
                        <p class="text-xs text-gray-500 mt-1">Submitted: <span id="monitoringSubmittedAt" class="font-semibold"></span></p>
                    </div>
                    <button onclick="closeMonitoringIpcrModal()" class="text-gray-500 hover:text-gray-700 ml-2 flex-shrink-0" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 text-xs sm:text-sm">
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Ratee:</span>
                        <span id="monitoringIpcrRatee" class="font-semibold text-gray-900 truncate"></span>
                    </div>
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Approved By:</span>
                        <input type="text" id="monitoringIpcrApprovedBy" class="text-sm font-semibold text-gray-900 border-0 border-b border-transparent bg-transparent px-1 py-0 w-full" value="" readonly>
                    </div>
                    <div class="flex flex-col sm:block">
                        <span class="text-gray-600">Noted By:</span>
                        <input type="text" id="monitoringIpcrNotedBy" class="text-sm font-semibold text-gray-900 border-0 border-b border-transparent bg-transparent px-1 py-0 w-full" value="" readonly>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span id="monitoringIpcrEmployee"></span>
                    <span class="mx-1">•</span>
                    <span id="monitoringIpcrDepartment"></span>
                </div>
            </div>

            <div class="overflow-x-auto px-2 sm:px-6 py-3 sm:py-4">
                <table class="w-full border-collapse min-w-[800px]">
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
                    <tbody id="monitoringIpcrTableBody">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-sm text-gray-400">Select a submission to preview.</td>
                        </tr>
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
        });

        window.openMonitoringIpcrModal = function(submissionId) {
            const modal = document.getElementById('monitoringIpcrDocumentContainer');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            fetch(`/director/monitoring/ipcr/${submissionId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (!data.success || !data.submission) {
                        throw new Error('Submission not found');
                    }

                    const submission = data.submission;
                    document.getElementById('monitoringIpcrTitle').value = submission.title || 'IPCR Submission';
                    document.getElementById('monitoringDisplaySchoolYear').textContent = submission.school_year || 'N/A';
                    document.getElementById('monitoringDisplaySemester').textContent = submission.semester || 'N/A';
                    document.getElementById('monitoringSubmittedAt').textContent = submission.submitted_at || 'N/A';
                    document.getElementById('monitoringIpcrApprovedBy').value = submission.approved_by || 'N/A';
                    document.getElementById('monitoringIpcrNotedBy').value = submission.noted_by || 'N/A';

                    const userName = submission.user && submission.user.name ? submission.user.name : 'Unknown';
                    const employeeId = submission.user && submission.user.employee_id ? submission.user.employee_id : 'N/A';
                    const deptCode = submission.user && submission.user.department ? submission.user.department.code : '';
                    const deptName = submission.user && submission.user.department ? submission.user.department.name : '';
                    document.getElementById('monitoringIpcrRatee').textContent = userName;
                    document.getElementById('monitoringIpcrEmployee').textContent = employeeId;
                    document.getElementById('monitoringIpcrDepartment').textContent = deptCode || deptName || 'N/A';

                    const tableBody = document.getElementById('monitoringIpcrTableBody');
                    if (tableBody) {
                        tableBody.innerHTML = submission.table_body_html || '';
                    }

                    document.querySelectorAll('#monitoringIpcrTableBody input, #monitoringIpcrTableBody textarea, #monitoringIpcrTableBody select').forEach(function(el) {
                        if (el.tagName === 'SELECT') {
                            el.disabled = true;
                        } else {
                            el.readOnly = true;
                        }
                        el.setAttribute('tabindex', '-1');
                        el.style.pointerEvents = 'none';
                    });
                })
                .catch(function() {
                    alert('Unable to load the IPCR submission.');
                });
        };

        window.closeMonitoringIpcrModal = function() {
            const modal = document.getElementById('monitoringIpcrDocumentContainer');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        };
    </script>
</body>
</html>
