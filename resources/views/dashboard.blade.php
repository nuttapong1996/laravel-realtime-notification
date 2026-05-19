<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            <!-- ไอคอนกระดิ่ง + ตัวเลข Badge -->
            <div class="relative inline-flex items-center p-2 cursor-pointer">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                
                @php
                    $unreadCount = auth()->user()->unreadNotifications->count();
                @endphp
                <span id="notification-badge" 
                      class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/4 -translate-y-1/4 {{ $unreadCount === 0 ? 'hidden' : '' }}">
                    {{ $unreadCount }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- ส่วนหัวของกล่องแจ้งเตือน -->
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-bold text-gray-700">รายการแจ้งเตือน</h3>
                    
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <button id="btn-mark-all" onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                            ทำเครื่องหมายว่าอ่านแล้วทั้งหมด
                        </button>
                    @endif
                </div>

                <!-- รายการแจ้งเตือน -->
                <ul id="notification-list" class="divide-y divide-gray-100">
                    @forelse(auth()->user()->notifications()->limit(15)->get() as $notification)
                        @php
                            $isUnread = $notification->unread();
                            $bgClass = $isUnread ? 'bg-blue-50 font-semibold' : 'bg-white text-gray-500';
                        @endphp

                        <li class="py-3 px-4 flex justify-between items-center rounded mb-1 {{ $bgClass }} transition-colors duration-300 shadow-sm" 
                            id="noti-{{ $notification->id }}">
                            
                            <div class="cursor-pointer flex-grow" onclick="markAsRead('{{ $notification->id }}')">
                                <span class="mr-2">🔔</span> {{ $notification->data['message'] }} 
                                <span class="text-xs text-gray-400 ml-2">({{ $notification->created_at->diffForHumans() }})</span>
                            </div>
                            
                            <button onclick="deleteNotification('{{ $notification->id }}')" class="text-red-400 hover:text-red-600 font-bold px-2 ml-4 text-xl" title="ลบการแจ้งเตือน">
                                &times;
                            </button>
                        </li>
                    @empty
                        <li id="no-notification" class="py-4 text-center text-gray-500 bg-gray-50 rounded">
                            ไม่มีการแจ้งเตือนใหม่
                        </li>
                    @endforelse
                </ul>

            </div>
        </div>
    </div>
</x-app-layout>