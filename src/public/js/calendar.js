// public/js/calendar.js
document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl || typeof FullCalendar === "undefined") return;

    const indexUrl =
        document.querySelector('meta[name="calendar-index-url"]')?.content ||
        window.location.pathname;

    const isMobile = () => window.innerWidth < 576;

    const cal = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "ja",
        headerToolbar: { left: "prev,next today", center: "title", right: "" },
        height: "auto",
        aspectRatio: isMobile() ? 0.95 : 1.35,
        dayHeaderFormat: { weekday: "short" },
        dayMaxEventRows: isMobile() ? 2 : 3,
        moreLinkClick: "popover",
        moreLinkContent: (arg) => `他${arg.num}件`,
        eventTimeFormat: { hour: "2-digit", minute: "2-digit", hour12: false },

        // Controller から渡されたイベント
        events: Array.isArray(window.CalendarData?.events)
            ? window.CalendarData.events
            : [],

        // 見やすいピル型に（時間 + タイトル）
        eventContent: function (arg) {
            const el = document.createElement("span");
            el.className = "hc-pill";
            const time = arg.event.allDay
                ? ""
                : `<span class="hc-time">${arg.timeText}</span>`;
            el.innerHTML = `${time}<span class="hc-title">${
                arg.event.title || ""
            }</span>`;
            return { domNodes: [el] };
        },

        // description を title 属性に
        eventDidMount: function (info) {
            const desc = (
                info.event.extendedProps.description || ""
            ).toString();
            if (desc) info.el.setAttribute("title", desc);
        },

        // 日付クリック → ?date=YYYY-MM-DD（Bladeで描画させる）
        dateClick: function (info) {
            const url = new URL(indexUrl, window.location.origin);
            url.searchParams.set("date", info.dateStr);
            window.location.href = url.toString();
        },

        // イベントクリック → 編集画面へ
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            window.location.href = `/calendar/${info.event.id}/edit`;
        },
    });

    cal.render();

    // 画面リサイズ時にモバイル最適値を反映
    let rid = null;
    window.addEventListener("resize", () => {
        clearTimeout(rid);
        rid = setTimeout(() => {
            cal.setOption("aspectRatio", isMobile() ? 0.95 : 1.35);
            cal.setOption("dayMaxEventRows", isMobile() ? 2 : 3);
        }, 150);
    });
});
