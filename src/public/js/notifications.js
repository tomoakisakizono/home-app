async function refreshUnreadCount() {
    try {
        const res = await fetch("/notifications/unread-count", {
            credentials: "same-origin",
        });
        const data = await res.json();
        const badge = document.getElementById("notif-badge");
        if (!badge) return;
        if (data.count > 0) {
            badge.textContent = data.count;
            badge.classList.remove("d-none");
        } else {
            badge.classList.add("d-none");
        }
    } catch (e) {
        /* noop */
    }
}

document.addEventListener("DOMContentLoaded", () => {
    refreshUnreadCount();
    setInterval(refreshUnreadCount, 30000); // 30秒ごと
});
