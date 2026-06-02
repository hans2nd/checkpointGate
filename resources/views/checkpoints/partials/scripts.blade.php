    <script>
        // ===== COLUMN VISIBILITY =====
        const STORAGE_KEY = 'checkpoint_col_prefs';
        const ALL_COLS = ['col-tanggal', 'col-nopol', 'col-vendor', 'col-kendaraan', 'col-barang', 'col-aktivitas',
            'col-penerimaan', 'col-start', 'col-end', 'col-gate', 'col-status', 'col-catatan', 'col-durasi',
            'col-penyerahan', 'col-durasi-dok'
        ];

        function getColPrefs() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                return saved ? JSON.parse(saved) : ALL_COLS.slice();
            } catch {
                return ALL_COLS.slice();
            }
        }

        function saveColPrefs(visible) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(visible));
        }

        function applyColumnVisibility() {
            const visible = getColPrefs();
            ALL_COLS.forEach(col => {
                const show = visible.includes(col);
                document.querySelectorAll(`[data-col="${col}"]`).forEach(el => {
                    el.style.display = show ? '' : 'none';
                });
            });
            // Sync checkboxes
            document.querySelectorAll('.col-toggle').forEach(cb => {
                cb.checked = visible.includes(cb.dataset.col);
            });
            // Update counter
            const hidden = ALL_COLS.length - visible.length;
            const counter = document.getElementById('colCount');
            counter.textContent = hidden > 0 ? (ALL_COLS.length - hidden) + '/' + ALL_COLS.length : ALL_COLS.length + '/' +
                ALL_COLS.length;
        }

        function resetColumns() {
            saveColPrefs(ALL_COLS.slice());
            applyColumnVisibility();
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyColumnVisibility();
            document.querySelectorAll('.col-toggle').forEach(cb => {
                cb.addEventListener('change', function() {
                    const prefs = getColPrefs();
                    if (this.checked) {
                        if (!prefs.includes(this.dataset.col)) prefs.push(this.dataset.col);
                    } else {
                        const idx = prefs.indexOf(this.dataset.col);
                        if (idx > -1) prefs.splice(idx, 1);
                    }
                    saveColPrefs(prefs);
                    applyColumnVisibility();
                });
            });
            // Close dropdown on click outside
            document.addEventListener('click', function(e) {
                const wrap = document.getElementById('colToggleWrap');
                const dd = document.getElementById('colDropdown');
                if (wrap && !wrap.contains(e.target)) dd.classList.add('hidden');
            });
        });

        // ===== BULK SELECT =====
        function toggleSelectAll() {
            const s = document.getElementById('selectAll');
            document.querySelectorAll('.row-checkbox').forEach(c => c.checked = s.checked);
            updateSelectedCount()
        }

        function updateSelectedCount() {
            const c = document.querySelectorAll('.row-checkbox:checked').length;
            const b = document.getElementById('bulkDeleteBtn');
            document.getElementById('selectedCount').textContent = c;
            if (c > 0) {
                b.classList.remove('hidden');
                b.classList.add('inline-flex')
            } else {
                b.classList.add('hidden');
                b.classList.remove('inline-flex')
            }
            const t = document.querySelectorAll('.row-checkbox').length;
            const s = document.getElementById('selectAll');
            s.checked = t > 0 && c === t;
            s.indeterminate = c > 0 && c < t
        }

        function doBulkDelete() {
            const ids = [...document.querySelectorAll('.row-checkbox:checked')].map(c => c.dataset.id);
            if (ids.length === 0) return;
            confirmBulkDelete('{{ route('checkpoints.bulk-delete') }}', ids)
        }

        // ===== GATE MODAL =====
        let selectedGateNumber = null;
        let currentCheckpointId = null;

        function openGateModal(checkpointId, noPolisi) {
            currentCheckpointId = checkpointId;
            const modal = document.getElementById('gateModal');
            const form = document.getElementById('gateForm');
            const subtitle = document.getElementById('gateModalSubtitle');
            const loading = document.getElementById('gateModalLoading');
            const content = document.getElementById('gateModalContent');

            form.action = '{{ url('checkpoints') }}/' + checkpointId + '/assign-gate';
            subtitle.textContent = '{{ __('Assign Gate') }}: ' + noPolisi;

            selectedGateNumber = null;
            document.getElementById('selectedGateInput').value = '';
            document.getElementById('selectedGateLabel').textContent = '-';
            document.getElementById('gateConfirmBtn').disabled = true;
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            modal.classList.remove('hidden');

            fetch('{{ route('gates.available') }}')
                .then(res => res.json())
                .then(gates => {
                    const frozenGrid = document.getElementById('gateGridFrozen');
                    const dryGrid = document.getElementById('gateGridDry');
                    frozenGrid.innerHTML = '';
                    dryGrid.innerHTML = '';

                    gates.forEach(gate => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        let label = '';

                        if (gate.nomor >= 1 && gate.nomor <= 16) {
                            label = 'F-' + gate.nomor;
                        } else if (gate.nomor >= 17 && gate.nomor <= 27) {
                            label = 'D-' + (gate.nomor - 16);
                        } else {
                            label = 'Gate-' + gate.nomor;
                        }

                        btn.textContent = label;
                        btn.dataset.gate = gate.nomor;

                        if (gate.available) {
                            btn.className =
                                'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                            btn.onclick = function() {
                                selectGate(gate.nomor);
                            };
                        } else {
                            btn.className =
                                'py-3 px-4 text-sm font-bold rounded-xl border border-red-200 bg-red-50 text-red-400 cursor-not-allowed opacity-60';
                            btn.disabled = true;
                            btn.title = 'Gate sedang digunakan';
                        }

                        if (gate.jenis_barang === 'FROZEN') frozenGrid.appendChild(btn);
                        else dryGrid.appendChild(btn);
                    });

                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error loading gates:', err);
                    loading.innerHTML = '<p class="text-sm text-red-500">Gagal memuat data gate.</p>';
                });
        }

        function selectGate(gateNumber) {
            selectedGateNumber = gateNumber;
            document.getElementById('selectedGateInput').value = gateNumber;
            document.getElementById('selectedGateLabel').textContent = 'Gate ' + gateNumber;
            document.getElementById('gateConfirmBtn').disabled = false;

            document.querySelectorAll('.gate-btn').forEach(btn => {
                if (parseInt(btn.dataset.gate) === gateNumber) {
                    btn.className =
                        'gate-btn py-3 px-4 text-sm font-bold rounded-xl border-2 border-orange-500 bg-orange-100 text-orange-700 ring-2 ring-orange-300 transition-all cursor-pointer';
                } else {
                    btn.className =
                        'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                }
            });
        }

        function closeGateModal() {
            document.getElementById('gateModal').classList.add('hidden');
        }

        // ===== CANCEL MODAL =====
        function openCancelModal(checkpointId, noPolisi) {
            const modal = document.getElementById('cancelModal');
            const form = document.getElementById('cancelForm');
            const subtitle = document.getElementById('cancelModalSubtitle');
            const note = document.getElementById('cancel_note');

            form.action = '{{ url('checkpoints') }}/' + checkpointId + '/cancel';
            subtitle.textContent = 'Cancel checkpoint: ' + noPolisi;
            note.value = '';
            modal.classList.remove('hidden');
            note.focus();
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // ===== TERIMA MODAL =====
        function openTerimaModal(checkpointId, noPolisi, kendaraan, tipe, barang, aktivitas, suratJalan, po, note) {
            const modal = document.getElementById('terimaModal');
            const form = document.getElementById('terimaForm');
            const subtitle = document.getElementById('terimaModalSubtitle');

            form.action = '{{ url('checkpoints') }}/' + checkpointId + '/trigger-penerimaan';
            subtitle.textContent = 'No Polisi: ' + noPolisi;

            document.getElementById('terima_jenis_kendaraan').value = kendaraan || '';
            document.getElementById('terima_tipe').value = tipe || '';
            document.getElementById('terima_jenis_barang').value = barang || '';
            document.getElementById('terima_aktivitas').value = aktivitas || '';
            document.getElementById('terima_no_surat_jalan').value = suratJalan || '';
            document.getElementById('terima_purchase_order').value = po || '';
            document.getElementById('terima_note').value = note || '';

            modal.classList.remove('hidden');
        }

        function closeTerimaModal() {
            document.getElementById('terimaModal').classList.add('hidden');
        }


        // ===== PRINT TICKET =====
        function handleGateSubmit(e) {
            if (document.getElementById('printTicketCheck').checked && currentCheckpointId) {
                // Get data from the table row
                const row = document.querySelector(`tr[data-cp-id="${currentCheckpointId}"]`);
                if (row) {
                    const now = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const waktuTerima = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear() + ' ' +
                        pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());

                    printGateTicket({
                        noPolisi: row.dataset.cpNopol,
                        vendor: row.dataset.cpVendor,
                        driver: row.dataset.cpDriver,
                        tipe: row.dataset.cpTipe,
                        jenisKendaraan: row.dataset.cpKendaraan,
                        jenisBarang: row.dataset.cpBarang,
                        aktivitas: row.dataset.cpAktivitas,
                        gate: selectedGateNumber,
                        tanggal: row.dataset.cpTanggal,
                        waktuTerima: waktuTerima,
                        printedBy: '{{ Auth::user()->name }}',
                    });
                }
            }
            // Allow form to submit normally
        }

        function printGateTicket(data) {
            const printWindow = window.open('', '_blank', 'width=450,height=640');

            let gateLabel = '';

            if (data.gate >= 1 && data.gate <= 16) {
                gateLabel = 'F-' + data.gate;
            } else if (data.gate >= 17 && data.gate <= 27) {
                gateLabel = 'D-' + (data.gate - 16);
            } else {
                gateLabel = 'Gate-' + data.gate;
            }

            printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Ticket Gate - ${data.noPolisi}</title>
<style>
    @page { size: 105mm 148.5mm; margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; width: 105mm; min-height: 148.5mm; padding: 6mm; background: #fff; color: #1a1a1a; }
    .ticket { border: 2px solid #222; border-radius: 8px; padding: 5mm; height: calc(148.5mm - 12mm); display: flex; flex-direction: column; }
    .header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 4mm; margin-bottom: 4mm; }
    .header h1 { font-size: 14pt; font-weight: 900; letter-spacing: 1px; margin-bottom: 1mm; }
    .header h2 { font-size: 9pt; color: #666; font-weight: 500; }
    .gate-badge { background: #222; color: #fff; font-size: 22pt; font-weight: 900; padding: 3mm 6mm; border-radius: 6px; display: inline-block; margin: 3mm 0 2mm; letter-spacing: 2px; }
    .info-grid { flex: 1; }
    .info-row { display: flex; border-bottom: 1px solid #eee; padding: 1.8mm 0; }
    .info-label { width: 35mm; font-size: 7.5pt; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-value { flex: 1; font-size: 8.5pt; font-weight: 700; }
    .badge { display: inline-block; padding: 0.8mm 2.5mm; border-radius: 3px; font-size: 7pt; font-weight: 700; }
    .badge-frozen { background: #e0f2fe; color: #0369a1; }
    .badge-dry { background: #fff7ed; color: #c2410c; }
    .badge-inbound { background: #fef3c7; color: #92400e; }
    .badge-outbound { background: #dbeafe; color: #1e40af; }
    .footer { border-top: 2px dashed #ccc; padding-top: 3mm; margin-top: 3mm; text-align: center; }
    .footer p { font-size: 6.5pt; color: #999; }
    .footer .printed-by { font-size: 7pt; color: #555; font-weight: 600; margin-top: 1mm; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style>
</head>
<body>
<div class="ticket">
    <div class="header">
        <h1>CHECKPOINT GIIC</h1>
        <h2>Ticket Penerimaan Dokumen</h2>
        <div class="gate-badge">GATE ${gateLabel}</div>
        
        <div style="margin-top: 3mm;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${gateLabel}" 
                style="width:30mm; height:30mm;" />
        </div>
    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">No. Polisi</span>
            <span class="info-value">${data.noPolisi}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vendor</span>
            <span class="info-value">${data.vendor}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Driver</span>
            <span class="info-value">${data.driver}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipe</span>
            <span class="info-value">${data.tipe}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jenis Kendaraan</span>
            <span class="info-value">${data.jenisKendaraan}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Storage</span>
            <span class="info-value"><span class="badge ${data.jenisBarang === 'FROZEN' ? 'badge-frozen' : 'badge-dry'}">${data.jenisBarang}</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">Aktivitas</span>
            <span class="info-value"><span class="badge ${data.aktivitas === 'INBOUND' ? 'badge-inbound' : 'badge-outbound'}">${data.aktivitas}</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">${data.tanggal}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu Terima</span>
            <span class="info-value">${data.waktuTerima}</span>
        </div>
    </div>
    <div class="footer">
        <div class="printed-by">Diterima oleh: ${data.printedBy}</div>
        <p>Dokumen ini sebagai bukti penerimaan dokumen dan kendaraan siap loading.</p>
        <p style="margin-top:1mm">Dicetak: ${data.waktuTerima}</p>
    </div>
</div>
<script>window.onload=function(){window.print();}<\/script>
</body>
</html>`);
            printWindow.document.close();
        }
    </script>
