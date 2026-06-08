<style>
    * {
        box-sizing: border-box;
    }

    /* ===== MONITORING CONTAINER ===== */
    #monitoring-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        gap: 8px;
    }

    /* ===== SLIDER ===== */
    .monitoring-slider {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    .slider-track-wrapper {
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    .slider-track {
        display: flex;
        height: 100%;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    .slider-slide {
        min-width: 100%;
        height: 100%;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        /* KUNCI SCALABLE FONT */
        container-type: size;
    }

    /* ===== SLIDE 1: SUMMARY ===== */
    .slide-1-content {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        gap: 1.5cqh;
        overflow: hidden;
        padding: 1cqh;
        --s1f: clamp(10px, 1.8cqh, 24px);
        font-size: var(--s1f);
    }

    #activity-card {
        flex: 0.6;
        display: flex;
        flex-direction: column;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }

    #activity-card .inner {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1.5em;
    }

    #activity-card table {
        width: 100%;
        border-collapse: collapse;
        flex: 1;
        height: 100%;
    }

    #activity-card th {
        font-size: 0.85em;
        font-weight: 600;
        color: #6b7280;
        padding: 0.4em 0.8em;
    }

    #activity-card td {
        padding: 0.4em 0.8em;
        vertical-align: middle;
    }

    #activity-card .stat-num {
        font-size: 1.4em;
        font-weight: 700;
    }

    #activity-card .lbl {
        font-size: 0.9em;
    }

#avg-card {
        flex: 1.4;
        display: flex;
            flex-direction: column;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }

    #avg-card .inner {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1.5em;
        overflow: hidden;
    }

    #avg-card .card-title {
        font-size: 1.1em;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.8em;
        flex-shrink: 0;
    }

    #avg-card table {
        width: 100%;
        border-collapse: collapse;
        flex: 1;
        height: 100%;
    }

    #avg-card th,
    #avg-card td {
        font-size: 0.8em;
        vertical-align: middle;
    }

    #avg-card thead th {
        position: relative;
        z-index: 6;
    }

    #avg-card .mono-val {
        font-size: 0.95em;
        font-weight: 700;
        font-family: monospace;
    }

    /* ===== SLIDE 2: GATES ===== */
    .slide-2-content {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        --gts: clamp(12px, 2cqw, 24px);
    }

    #gate-section {
        flex: 1;
        min-height: 0;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #gate-grid-body {
        flex: 1;
        min-height: 0;
        padding: 1cqh 1cqw;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #gate-unified-grid {
        flex: 1;
        min-height: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1cqw;
        flex-direction: row;
    }

    .gate-side {
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .frozen-side-wrapper {
        border-right: 1px solid #e5e7eb;
        padding-right: 1cqw;
    }

    .gate-side-title {
        text-align: center;
        font-weight: 700;
        margin-bottom: 0.5cqh;
        flex-shrink: 0;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-size: var(--gts);
    }

    #frozen-grid-inner {
        flex: 1;
        min-height: 0;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 6px;
    }

    #dry-grid-inner {
        flex: 1;
        min-height: 0;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(3, 1fr);
        gap: 6px;
    }

    /* ===== GATE CARD (SCALABLE & STRETCH FIX) ===== */
    .gate-card {
        border-radius: 6px;
        border-width: 1.5px;
        border-style: solid;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 0;
        container-type: size;
    }

    .gate-card-header {
        text-align: center;
        padding: 2px 3px;
        flex-shrink: 0;
        height: max(20px, 20cqh);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .gate-card-header .gate-number {
        font-size: clamp(10px, 14cqmin, 24px);
        font-weight: 800;
        line-height: 1;
        color: #fff;
        white-space: nowrap;
    }

    .gate-card-header .gate-type {
        font-size: clamp(7px, 8cqmin, 12px);
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
        white-space: nowrap;
        line-height: 1;
    }

    .gate-body {
        flex: 1;
        min-height: 0;
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        padding: 3px;
        background: #fff;
        gap: 2px;
    }

    /* Kolom Kiri (Info) */
    .gate-body-inner {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        /* Supaya Aktivitas diam di atas */
        align-items: center;
        text-align: center;
        gap: 2px;
        padding: 2px 0;
        overflow: hidden;
    }

    /* Teks dibuat normal (bukan nowrap) agar full terlihat & membungkus (wrap) jika kepanjangan */
    .gate-aktivitas {
        font-size: clamp(10px, 12cqw, 18px);
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        text-align: center;
        line-height: 1.1;
        width: fit-content;
    }

    .gate-plate {
        font-size: clamp(10px, 16cqw, 24px);
        font-weight: 800;
        color: #1f2937;
        line-height: 1.1;
        white-space: normal;
        /* Menghilangkan titik-titik (ellipsis) */
        word-wrap: break-word;
        width: 100%;
    }

    .gate-vendor-name {
        font-size: clamp(7px, 9cqw, 14px);
        color: #6b7280;
        line-height: 1.1;
        white-space: normal;
        /* Menghilangkan titik-titik (ellipsis) */
        word-wrap: break-word;
        width: 100%;
    }

    .gate-badge {
        font-size: clamp(7px, 10cqw, 13px);
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 9999px;
        white-space: normal;
        text-align: center;
        line-height: 1.1;
        margin-bottom: 2px;
    }

    .gate-timer-text {
        font-size: clamp(8px, 10cqw, 15px);
        font-weight: 700;
        font-family: monospace;
        padding: 1px 4px;
        border-radius: 3px;
        white-space: nowrap;
    }

    .gate-empty {
        color: #d1d5db;
        font-style: italic;
        font-size: clamp(8px, 12cqw, 16px);
        margin: auto 0;
    }

    /* Kolom Kanan (Waktu) */
    .gate-times {
        flex: 0 0 32%;
        /* Proporsi sedikit diturunkan krn format tumpuk */
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        border-left: 1px solid #e5e7eb;
        padding-left: 2px;
        color: #6b7280;
        font-family: monospace;
        overflow: hidden;
    }

    .gate-times>div {
        display: flex;
        flex-direction: column;
        align-items: center;
        /* Teks dan icon ke tengah (center alignment) */
        justify-content: center;
        text-align: center;
        font-size: clamp(7px, 8cqmin, 13px);
        /* Ukuran jam */
        line-height: 1;
    }

    .gate-times>div>span:first-child {
        font-size: clamp(9px, 10cqmin, 16px);
        /* Ukuran icon sedikit diperbesar */
        margin-bottom: 2px;
        /* Jarak antara icon dan waktu */
    }

    /* ===== GATE LEGEND ===== */
    .gate-legend {
        flex-shrink: 0;
        margin-top: 6px;
        background: #fff;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        padding: 6px 10px;
    }

    .gate-legend p {
        font-size: clamp(10px, 1cqw, 14px);
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: clamp(10px, 1cqw, 13px);
        color: #6b7280;
    }

    /* ===== SLIDER INDICATORS ===== */
    .slider-indicators {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 5px 0 2px;
        flex-shrink: 0;
    }

    .slider-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #d1d5db;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        padding: 0;
    }

    .slider-dot.active {
        background: linear-gradient(135deg, #f97316, #ea580c);
        transform: scale(1.3);
        box-shadow: 0 0 8px rgba(249, 115, 22, 0.4);
    }

    .slider-dot:hover:not(.active) {
        background: #9ca3af;
        transform: scale(1.1);
    }

    .slider-progress-wrap {
        width: 100px;
        height: 3px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-left: 10px;
    }

    .slider-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #f97316, #ea580c);
        border-radius: 3px;
        transition: width 0.2s linear;
    }

    .slider-label {
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
        cursor: pointer;
        transition: color 0.3s;
    }

    .slider-label.active {
        color: #f97316;
    }

    /* ===== FULLSCREEN ===== */
    body.fullscreen-mode #monitoring-container {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #f9fafb;
        padding: 8px 12px;
        height: 100vh;
    }




@media screen and (max-width: 480px) {

    /* ===== GLOBAL SLIDE FIX ===== */
    /* Let each slide scroll vertically on its own */
    .slider-slide {
        min-width: 100% !important;
        height: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* ===== SLIDE 1: SUMMARY (mobile) ===== */
    .slide-1-content {
        flex: none !important;
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        padding: 8px;
        gap: 10px;
        font-size: 12px;
    }

    #activity-card {
        flex: none !important;
        min-height: auto !important;
        overflow: visible !important;
        
    }

    #activity-card .inner {
        flex: none !important;
        overflow-x: auto !important;
        overflow-y: visible !important;
        padding: 10px;
    }

    #activity-card table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        flex: none !important;
        height: auto !important;
    }

    #avg-card {
        flex: none !important;
        min-height: auto !important;
        overflow: visible !important;
    }

    #avg-card .inner {
        flex: none !important;
        overflow-x: auto !important;
        overflow-y: visible !important;
        padding: 10px;
    }

    /* Disable sticky Kendaraan column on mobile — rowspan + sticky causes overlap bugs */
    #avg-card thead th,
    #avg-card tbody td {
        position: static !important;
        z-index: auto !important;
    }

    /* ===== SLIDE 2: GATES (mobile) ===== */
    .slide-2-content {
        flex: none !important;
        min-height: auto !important;
        height: auto !important;
    }

    #gate-section {
        flex: none !important;
        min-height: auto !important;
        overflow: visible !important;
    }

    #gate-grid-body {
        flex: none !important;
        min-height: auto !important;
        overflow: visible !important;
        padding: 8px;
    }

    /* Stack Frozen and Dry in a single column */
    #gate-unified-grid {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        min-height: auto !important;
        flex: none !important;
    }

    /* Each side flows naturally, no nested scroll */
    .gate-side {
        display: flex;
        flex-direction: column;
        min-height: auto !important;
        overflow: visible !important;
    }

    .frozen-side-wrapper {
        border-right: none !important;
        padding-right: 0 !important;
    }

    .gate-side-title {
        font-size: 16px !important;
    }

    /* Grid auto-rows so cards have consistent height */
    #frozen-grid-inner,
    #dry-grid-inner {
        flex: none !important;
        min-height: auto !important;
        grid-template-columns: repeat(4, 1fr) !important;
        grid-template-rows: auto !important;
        gap: 6px;
    }

    .gate-card {
        height: 120px;
    }

    /* Legend at the bottom, no overlap */
    .gate-legend {
        margin-top: 8px;
    }
}



/* HD sampai 4K — sama semua, tulis sekali */


/* Activity card lebih compact */
#activity-card .inner {
    padding: 0.8em 1.2em;
}

#activity-card th {
    font-size: 0.75em;
    padding: 0.3em 0.6em;
}

#activity-card td {
    padding: 0.25em 0.6em;
}

#activity-card .stat-num {
    font-size: 1.1em;  /* dari 1.4em */
    font-weight: 700;
}

#activity-card .lbl {
    font-size: 0.8em;
}

/* Kurangi porsi activity card, besarkan avg */
#activity-card {
    flex: 0.4;  /* dari 0.6 */
}

#avg-card {
    flex: 1.6;  /* dari 1.4 */
}

    #avg-card {
        flex: 1;
        display: flex;
        flex-direction: column;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }

    #avg-card .inner {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1.5em;
        overflow: hidden;
    }

    #avg-card table {
        width: 100%;
        border-collapse: collapse;
        flex: 1;
        height: 100%;
    }

    #avg-card th,
    #avg-card td {
        font-size: 0.8em;
        vertical-align: middle;
    }

    #avg-card .mono-val {
        font-size: 0.95em;
        font-weight: 700;
        font-family: monospace;
    }




</style>
