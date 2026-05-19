document.addEventListener('DOMContentLoaded', () => {
    // 1. ดึงค่า Token และ User ID จาก Meta Tags
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const userId = userIdMeta ? userIdMeta.getAttribute('content') : null;

    // 2. ผูกฟังก์ชันเข้ากับ window object (จำเป็นต้องทำเพราะเราเรียกใช้ผ่าน onclick ใน HTML)
    window.markAsRead = function(id) {
        const notiElement = document.getElementById(`noti-${id}`);
        if (!notiElement || !notiElement.classList.contains('bg-blue-50')) return;

        fetch(`/notifications/${id}/read`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                notiElement.classList.remove('bg-blue-50', 'font-semibold');
                notiElement.classList.add('bg-white', 'text-gray-500');
                updateBadgeCount(-1);
            }
        });
    };

    window.markAllAsRead = function() {
        fetch(`/notifications/read-all`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.bg-blue-50').forEach(el => {
                    el.classList.remove('bg-blue-50', 'font-semibold');
                    el.classList.add('bg-white', 'text-gray-500');
                });
                
                const btn = document.getElementById('btn-mark-all');
                if(btn) btn.style.display = 'none';

                updateBadgeCount(0, true);
            }
        });
    };

    window.deleteNotification = function(id) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const notiElement = document.getElementById(`noti-${id}`);
                
                if (notiElement.classList.contains('bg-blue-50')) {
                    updateBadgeCount(-1);
                }

                notiElement.style.transition = "opacity 0.3s";
                notiElement.style.opacity = "0";
                setTimeout(() => {
                    notiElement.remove();
                    const list = document.getElementById('notification-list');
                    if (list && list.children.length === 0) {
                        list.innerHTML = '<li id="no-notification" class="py-4 text-center text-gray-500 bg-gray-50 rounded">ไม่มีการแจ้งเตือนใหม่</li>';
                    }
                }, 300);
            }
        });
    };

    function updateBadgeCount(change, setZero = false) {
        const badge = document.getElementById('notification-badge');
        if (!badge) return;

        let currentCount = parseInt(badge.innerText) || 0;
        
        if (setZero) {
            currentCount = 0;
        } else {
            currentCount += change;
        }

        if (currentCount > 0) {
            badge.innerText = currentCount;
            badge.classList.remove('hidden');
            
            if (change > 0) {
                badge.classList.add('animate-bounce');
                setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
            }
        } else {
            badge.innerText = 0;
            badge.classList.add('hidden');
        }
    }

    // 3. เริ่มต้นดักฟัง WebSockets ถ้ามีการล็อกอิน (มี userId)
    if (userId && window.Echo) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                const noNoti = document.getElementById('no-notification');
                if (noNoti) noNoti.remove();

                updateBadgeCount(1);

                const list = document.getElementById('notification-list');
                if (!list) return; // ถ้าไม่ได้อยู่หน้า Dashboard ก็ไม่ต้องหา list เพื่อแทรก HTML

                const li = document.createElement('li');
                li.id = `noti-${notification.id}`;
                li.className = 'py-3 px-4 flex justify-between items-center rounded mb-1 bg-blue-50 font-semibold transition-colors duration-300 shadow-sm';
                
                li.innerHTML = `
                    <div class="cursor-pointer flex-grow" onclick="markAsRead('${notification.id}')">
                        <span class="mr-2">🔔</span> ${notification.message} 
                        <span class="text-xs text-gray-400 ml-2">(เมื่อสักครู่)</span>
                    </div>
                    <button onclick="deleteNotification('${notification.id}')" class="text-red-400 hover:text-red-600 font-bold px-2 ml-4 text-xl" title="ลบการแจ้งเตือน">
                        &times;
                    </button>
                `;
                
                list.insertBefore(li, list.firstChild);
            });
    }
});