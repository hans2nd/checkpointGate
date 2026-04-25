<script>
    // =============================================
    // HELPERS
    // =============================================
    function formatTime(iso) {
        if (!iso) return '--:--';
        const d = new Date(iso);
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    function getDurationMinutes(startISO, endISO) {
        return ((endISO ? new Date(endISO) : new Date()) - new Date(startISO)) / 60000;
    }

    function formatDuration(startISO, endISO) {
        const totalSec = Math.max(0, Math.floor(((endISO ? new Date(endISO) : new Date()) - new Date(startISO)) /
            1000));
        const h = Math.floor(totalSec / 3600);
        const m = Math.floor((totalSec % 3600) / 60);
        const s = totalSec % 60;
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    function getDurationBg(minutes) {
        if (minutes > 60) return '#FEE2E2';
        if (minutes > 30) return '#FEF9C3';
        return '#D1FAE5';
    }

    // =============================================
    // GATE COLORS & TIMERS
    // =============================================
    function applyGateColors() {
        document.querySelectorAll('.gate-body').forEach(body => {
            const ws = body.dataset.waktuStart,
                we = body.dataset.waktuEnd,
                st = body.dataset.status;
            if (!ws) {
                body.style.backgroundColor = '';
                return;
            }
            let mins;
            if (st === 'ON LOADING') mins = getDurationMinutes(ws, null);
            else if (st === 'FINISH' && we) mins = getDurationMinutes(ws, we);
            else {
                body.style.backgroundColor = '';
                return;
            }
            body.style.backgroundColor = getDurationBg(mins);
        });
        document.querySelectorAll('.static-timer').forEach(t => {
            if (t.dataset.start && t.dataset.end) t.textContent = formatDuration(t.dataset.start, t.dataset
                .end);
        });
        document.querySelectorAll('.gate-timer').forEach(t => {
            if (t.dataset.start) t.textContent = formatDuration(t.dataset.start, null);
        });
    }

    function updateTimers() {
        document.querySelectorAll('.gate-timer').forEach(t => {
            if (!t.dataset.start) return;
            t.textContent = formatDuration(t.dataset.start, null);
            const body = t.closest('.gate-body');
            if (body) body.style.backgroundColor = getDurationBg(getDurationMinutes(t.dataset.start, null));
        });
    }

    // =============================================
    // GATE CONTENT BUILDER (live refresh)
    // =============================================
    function getStatusHtml(gate) {
        if (gate.waktu_penyerahan)
            return '<span class="gate-badge bg-purple-100 text-purple-700">✅ COMPLETED</span>';
        if (gate.waktu_end) {
            const dur = gate.waktu_start ? formatDuration(gate.waktu_start, gate.waktu_end) : 'DONE';
            return `<span class="static-timer gate-timer-text bg-emerald-100 text-emerald-700" data-start="${gate.waktu_start||''}" data-end="${gate.waktu_end}">${dur}</span><span class="gate-badge bg-emerald-100 text-emerald-700">🏁 FINISH</span>`;
        }
        if (gate.waktu_start)
            return `<span class="gate-timer gate-timer-text bg-blue-100 text-blue-700" data-start="${gate.waktu_start}">${formatDuration(gate.waktu_start,null)}</span><span class="gate-badge animate-pulse bg-blue-100 text-blue-700">⏳ ${gate.aktivitas === 'INBOUND' ? 'UNLOADING' : 'LOADING'}</span>`;
        if (gate.waktu_penerimaan)
            return '<span class="gate-badge bg-yellow-100 text-yellow-700">📋 ASSIGN</span>';
        return '';
    }

    function buildGateContent(gate, gateIndex) {
        const id = (gate && gate.nomor) ? gate.nomor : gateIndex;
        const body = document.querySelector('#gate-' + id + ' .gate-body');
        if (!body) return;

        if (!gate || !gate.no_polisi) {
            body.innerHTML = '<div class="gate-body-inner"><span class="gate-empty">kosong</span></div>';
            body.style.backgroundColor = '';
            return;
        }

        body.dataset.status = gate.status || '';
        body.dataset.waktuStart = gate.waktu_start || '';
        body.dataset.waktuEnd = gate.waktu_end || '';

        if (gate.waktu_penyerahan && ((new Date() - new Date(gate.waktu_penyerahan)) / 60000) >= 2) {
            body.innerHTML = '<div class="gate-body-inner"><span class="gate-empty">kosong</span></div>';
            body.style.backgroundColor = '';
            return;
        }

        const act = (gate.aktivitas || '').toUpperCase();
        let aktHtml = '';
        if (act === 'INBOUND') aktHtml = '<span class="gate-aktivitas bg-orange-100 text-orange-700">INBOUND</span>';
        else if (act === 'OUTBOUND') aktHtml =
            '<span class="gate-aktivitas bg-indigo-100 text-indigo-700">OUTBOUND</span>';
        else if (gate.aktivitas) aktHtml =
            `<span class="gate-aktivitas bg-gray-100 text-gray-700">${gate.aktivitas}</span>`;

        const timesHtml = `
    <div class="gate-times">
        <div style="color:${gate.waktu_penerimaan?'#4b5563':'#d1d5db'}">
            <span>📋</span>
            <span>${gate.waktu_penerimaan?formatTime(gate.waktu_penerimaan):'--:--'}</span>
        </div>
        <div style="color:${gate.waktu_start?'#4b5563':'#d1d5db'}">
            <span>⏳</span>
            <span>${gate.waktu_start?formatTime(gate.waktu_start):'--:--'}</span>
        </div>
        <div style="color:${gate.waktu_end?'#4b5563':'#d1d5db'}">
            <span>🏁</span>
            <span>${gate.waktu_end?formatTime(gate.waktu_end):'--:--'}</span>
        </div>
        <div style="color:${gate.waktu_penyerahan?'#4b5563':'#d1d5db'}">
            <span>✅</span>
            <span>${gate.waktu_penyerahan?formatTime(gate.waktu_penyerahan):'--:--'}</span>
        </div>
    </div>`;

        body.innerHTML = `
    <div class="gate-body-inner">
        ${aktHtml}
        <div style="margin: auto 0; width: 100%;">
            <p class="gate-plate">${gate.no_polisi}</p>
            <p class="gate-vendor-name">${gate.vendor||''}</p>
        </div>
        ${getStatusHtml(gate)}
    </div>
    ${timesHtml}`;

        if (gate.status === 'ON LOADING' && gate.waktu_start)
            body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, null));
        else if (gate.status === 'FINISH' && gate.waktu_start && gate.waktu_end)
            body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, gate.waktu_end));
        else
            body.style.backgroundColor = '';
    }

    // =============================================
    // FULLSCREEN
    // =============================================
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.error(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', () => {
        const isFs = !!document.fullscreenElement;
        document.body.classList.toggle('fullscreen-mode', isFs);
        document.querySelector('.fs-icon-expand').style.display = isFs ? 'none' : '';
        document.querySelector('.fs-icon-compress').style.display = isFs ? '' : 'none';
        const exitBtn = document.querySelector('.fs-exit-btn');
        if (exitBtn) exitBtn.style.display = isFs ? 'flex' : 'none';
        
        const container = document.getElementById('monitoring-container');
        const footer = document.querySelector('footer');
        const footerHeight = footer ? footer.offsetHeight : 0;
        
        if (isFs) {
            // 24px accounts for main padding (12px top and bot)
            container.style.height = `calc(100vh - ${24 + footerHeight}px)`;
        } else {
            // Previously 120px accounted for header and standard padding
            container.style.height = `calc(100vh - ${120 + footerHeight}px)`;
        }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && document.fullscreenElement) toggleFullscreen();
    });

    // =============================================
    // SLIDER ENGINE
    // =============================================
    const SLIDE_INTERVAL = 10000;
    const PROGRESS_TICK = 100;
    let currentSlide = 0;
    const totalSlides = 2;
    let progressTimer = null;
    let progressValue = 0;
    let sliderPaused = false;

    const track = document.getElementById('slider-track');
    const dots = document.querySelectorAll('.slider-dot');
    const labels = document.querySelectorAll('.slider-label');
    const progressBar = document.getElementById('slider-progress');
    const sliderEl = document.getElementById('monitoring-slider');

    function goToSlide(index) {
        currentSlide = ((index % totalSlides) + totalSlides) % totalSlides;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        document.querySelectorAll('.slider-slide').forEach((s, i) => s.classList.toggle('active', i === currentSlide));
        dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
        labels.forEach(l => l.classList.toggle('active', parseInt(l.dataset.goto) === currentSlide));
        progressValue = 0;
        if (progressBar) progressBar.style.width = '0%';
    }

    function startSliderAutoplay() {
        if (progressTimer) clearInterval(progressTimer);
        progressValue = 0;
        progressTimer = setInterval(() => {
            if (sliderPaused) return;
            progressValue += PROGRESS_TICK;
            if (progressBar) progressBar.style.width = Math.min((progressValue / SLIDE_INTERVAL) * 100, 100) +
                '%';
            if (progressValue >= SLIDE_INTERVAL) {
                goToSlide(currentSlide + 1);
                progressValue = 0;
            }
        }, PROGRESS_TICK);
    }

    dots.forEach(d => d.addEventListener('click', () => {
        goToSlide(parseInt(d.dataset.goto));
        startSliderAutoplay();
    }));
    labels.forEach(l => l.addEventListener('click', () => {
        goToSlide(parseInt(l.dataset.goto));
        startSliderAutoplay();
    }));
    sliderEl.addEventListener('mouseenter', () => sliderPaused = true);
    sliderEl.addEventListener('mouseleave', () => sliderPaused = false);
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft') {
            goToSlide(currentSlide - 1);
            startSliderAutoplay();
        }
        if (e.key === 'ArrowRight') {
            goToSlide(currentSlide + 1);
            startSliderAutoplay();
        }
    });

    // =============================================
    // INIT
    // =============================================

    // Wait for DOM paint 
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            applyGateColors();
            startSliderAutoplay();
        });
    });

    setInterval(updateTimers, 1000);

    document.getElementById('tanggal-picker').addEventListener('change', function() {
        if (this.value) window.location.href = '{{ route('livemonitoring') }}?tanggal=' + this.value;
    });

    // =============================================
    // LIVE REFRESH (today only)
    // =============================================
    @if ($isToday)
        setInterval(function() {
            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            const curDate = `${yyyy}-${mm}-${dd}`;

            // Update picker display if day changed (midnight rollover)
            const picker = document.getElementById('tanggal-picker');
            if (picker && picker.value !== curDate) {
                picker.value = curDate;
            }

            fetch('{{ route('livemonitoring.data') }}?tanggal=' + curDate)
                .then(r => r.json())
                .then(data => {
                    for (let i = 1; i <= 27; i++) buildGateContent(data.gates[i], i);

                    if (data.activitySummary) {
                        const rows = document.querySelectorAll('#activity-summary-body tr');
                        data.activitySummary.forEach((item, idx) => {
                            if (!rows[idx]) return;
                            const cells = rows[idx].querySelectorAll('td');
                            [item.parking ?? 0, item.receiving ?? 0, item.onProcess ?? item
                                .on_process ?? 0, item.finish ?? 0, item.total ?? 0
                            ].forEach((v, ci) => {
                                if (cells[ci + 1]) {
                                    const s = cells[ci + 1].querySelector('span');
                                    if (s) {
                                        s.textContent = v;
                                        if (ci === 4) {
                                            // Total column — bold amber
                                            s.style.fontWeight = '700';
                                            s.style.color = v > 0 ? '#f59e0b' : '#d1d5db';
                                        } else {
                                            s.className =
                                                `stat-num ${v>0?'text-gray-800':'text-gray-300'}`;
                                        }
                                    }
                                }
                            });
                        });
                    }

                    if (data.avgTimes) {
                        const avgBody = document.getElementById('avg-times-body');
                        if (avgBody) {
                            const gc = (t, lm) => !t ? 'color:#d1d5db' : !lm ? 'color:#374151' : t > lm ?
                                'color:#dc2626' : t < lm ? 'color:#16a34a' : 'color:#374151';
                            avgBody.innerHTML = data.avgTimes.map(row => `
                    <tr style="border-top:1px solid #f9fafb">
                        <td style="padding:3px 8px 3px 0;font-weight:500;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap">${row.jenis_kendaraan}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.DRY_ALT,row.DRY_ALT_LMONTH)}">${row.DRY_ALT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.DRY_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.DRY_ALT_LMONTH??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.DRY_AUT,row.DRY_AUT_LMONTH)}">${row.DRY_AUT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.DRY_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.DRY_AUT_LMONTH??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.FROZEN_ALT,row.FROZEN_ALT_LMONTH)}">${row.FROZEN_ALT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.FROZEN_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.FROZEN_ALT_LMONTH??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.FROZEN_AUT,row.FROZEN_AUT_LMONTH)}">${row.FROZEN_AUT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.FROZEN_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.FROZEN_AUT_LMONTH??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.CHILLED_ALT,row.CHILLED_ALT_LMONTH)}">${row.CHILLED_ALT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.CHILLED_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.CHILLED_ALT_LMONTH??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.CHILLED_AUT,row.CHILLED_AUT_LMONTH)}">${row.CHILLED_AUT??'—'}</td>
                        <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.CHILLED_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.CHILLED_AUT_LMONTH??'—'}</td>
                    </tr>`).join('');
                        }
                    }
                    document.getElementById('last-update').textContent = 'Terakhir: ' + new Date()
                        .toLocaleTimeString('id-ID');
                })
                .catch(err => console.error('Refresh error:', err));
        }, 5000);
    @endif
</script>
