<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">กล่องแจ้งเตือน Real-time</h3>

                <!-- ส่วนแสดงข้อความแบบ Real-time -->
                <div id="notification-alert" class="hidden mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow">
                    🔔 <strong>แจ้งเตือนใหม่:</strong> <span id="alert-text"></span>
                </div>

                <!-- รายการแจ้งเตือนทั้งหมด -->
                <ul id="notification-list" class="divide-y divide-gray-200">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <li class="py-3 text-gray-700">
                            {{ $notification->data['message'] }}
                            <span
                                class="text-xs text-gray-400">({{ $notification->created_at->diffForHumans() }})</span>
                        </li>
                    @empty
                        <li id="no-notification" class="py-3 text-gray-500">ยังไม่มีการแจ้งเตือน</li>
                    @endforelse
                </ul>

            </div>
        </div>
    </div>

    <!-- เรียกใช้ Laravel Echo เพื่อดักฟัง Event -->
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            const userId = "{{ auth()->id() }}";

            if (userId) {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('ได้รับแจ้งเตือน:', notification);

                        // 1. ซ่อนข้อความ "ยังไม่มีการแจ้งเตือน" (ถ้ามี)
                        const noNoti = document.getElementById('no-notification');
                        if (noNoti) noNoti.remove();

                        // 2. แสดงกล่องแจ้งเตือนด้านบนแบบวูบวาบ
                        const alertBox = document.getElementById('notification-alert');
                        const alertText = document.getElementById('alert-text');
                        alertText.innerText = notification.message;
                        alertBox.classList.remove('hidden');

                        // 3. อัปเดตรายการลงใน List ด้านล่าง
                        const list = document.getElementById('notification-list');
                        const li = document.createElement('li');
                        li.className = 'py-3 text-gray-700 font-semibold bg-yellow-50 px-2 rounded mt-1';
                        li.innerHTML =
                            `🔔 ${notification.message} <span class="text-xs text-gray-400">(${notification.created_at})</span>`;
                        list.insertBefore(li, list.firstChild);
                    });
            }
        });
    </script>
</x-app-layout>
