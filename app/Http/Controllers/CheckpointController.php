<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Gate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CheckpointController extends Controller
{
    public function index(Request $request)
    {
        $query = Checkpoint::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('driver', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($aktivitas = $request->input('aktivitas')) {
            $query->where('aktivitas', $aktivitas);
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('tanggal')) {
            $query->whereDate('tanggal', $date);
        }

        $perPage = in_array($request->input('per_page'), [10,15,25,50,100,1000]) ? (int)$request->input('per_page') : 15;

        $checkpoints = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('checkpoints.index', compact('checkpoints'));
    }

    public function create()
    {
        return view('checkpoints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|string|max:20',
            'vendor' => 'required|string|max:100',
            'driver' => 'required|string|max:100',
            'tipe' => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'jenis_barang' => 'required|in:FROZEN,DRY,CHILLED',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
            'gate' => 'nullable|string|max:30',
            'note' => 'nullable|string|max:500',
            'no_surat_jalan' => 'nullable|string|max:100',
            'purchase_order' => 'nullable|string|max:100',
        ]);

        $validated['tanggal'] = Carbon::today();
        $validated['status'] = 'START';
        $validated['created_by'] = Auth::id();

        // Auto-insert Master Vehicle jika belum ada
        \App\Models\Vehicle::firstOrCreate(
            ['no_polisi' => $validated['no_polisi']],
            [
                'driver' => $validated['driver'],
                'vendor' => $validated['vendor'],
                'tipe' => $validated['tipe'],
                'jenis_kendaraan' => $validated['jenis_kendaraan'],
            ]
        );

        Checkpoint::create($validated);

        return redirect()->route('checkpoints.index')
                         ->with('success', 'Data checkpoint berhasil ditambahkan.');
    }

    public function show(Checkpoint $checkpoint)
    {
        return view('checkpoints.show', compact('checkpoint'));
    }

    public function edit(Checkpoint $checkpoint)
    {
        return view('checkpoints.edit', compact('checkpoint'));
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_polisi' => 'required|string|max:20',
            'vendor' => 'required|string|max:100',
            'driver' => 'required|string|max:100',
            'tipe' => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'waktu_penerimaan_dokumen' => 'nullable|date|after_or_equal:tanggal',
            'waktu_penyerahan_dokumen' => 'nullable|date|after_or_equal:tanggal',
            'jenis_barang' => 'required|in:FROZEN,DRY,CHILLED',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
            'gate' => 'nullable|string|max:30',
            'status' => 'required|in:START,FINISH,ON LOADING,CANCEL',
            'waktu_start' => 'nullable|date|before_or_equal:waktu_end|after_or_equal:waktu_penerimaan_dokumen',
            'waktu_end' => 'nullable|date|after_or_equal:waktu_start',
            'note' => 'nullable|string|max:500',
            'no_surat_jalan' => 'nullable|string|max:100',
            'purchase_order' => 'nullable|string|max:100',
            'cancel_note' => 'required_if:status,CANCEL|nullable|string|max:500',
        ], [
            'waktu_penerimaan_dokumen.after_or_equal' => 'Waktu penerimaan dokumen tidak boleh kurang dari tanggal.',
            'waktu_penyerahan_dokumen.after_or_equal' => 'Waktu penyerahan dokumen tidak boleh kurang dari tanggal.',
            'waktu_start.before_or_equal' => 'Waktu start tidak boleh lebih besar dari waktu end.',
            'waktu_end.after_or_equal' => 'Waktu end tidak boleh kurang dari waktu start.',
            'waktu_start.after_or_equal' => 'Waktu start tidak boleh kurang dari tanggal penerimaan dokumen.',
        ]);

        if ($validated['status'] === 'CANCEL') {
            $validated['canceled_at'] = $checkpoint->canceled_at ?? Carbon::now();
            $validated['canceled_by'] = $checkpoint->canceled_by ?? Auth::id();
        } elseif ($checkpoint->status === 'CANCEL') {
            $validated['cancel_note'] = null;
            $validated['canceled_at'] = null;
            $validated['canceled_by'] = null;
        }

        // Auto-calculate durasi from waktu_start and waktu_end
        if (!empty($validated['waktu_start']) && !empty($validated['waktu_end'])) {
            $start = Carbon::parse($validated['waktu_start']);
            $end = Carbon::parse($validated['waktu_end']);
            $diff = $start->diff($end);
            $hours = ($diff->days * 24) + $diff->h;
            $validated['durasi'] = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
        } else {
            $validated['durasi'] = null;
        }

        $checkpoint->update($validated);

        return redirect()->route('checkpoints.index')
                         ->with('success', 'Data checkpoint berhasil diperbarui.');
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('checkpoints.index')
                         ->with('success', 'Data checkpoint berhasil dihapus.');
    }

    /**
     * Bulk delete checkpoints
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        Checkpoint::whereIn('id', $request->ids)->delete();

        return redirect()->route('checkpoints.index')
                         ->with('success', count($request->ids) . ' data checkpoint berhasil dihapus.');
    }

    /**
     * Export checkpoints to Excel
     */
    public function export(Request $request)
    {
        $query = Checkpoint::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('driver', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($aktivitas = $request->input('aktivitas')) {
            $query->where('aktivitas', $aktivitas);
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('tanggal')) {
            $query->whereDate('tanggal', $date);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Checkpoint');

        // Header
        $headers = ['No', 'Tanggal', 'No Polisi', 'Vendor', 'Kendaraan', 'Barang', 'Aktivitas', 'Penerimaan Dokumen', 'Waktu Tunggu', 'Start Loading', 'End Loading', 'Gate', 'Status', 'Catatan', 'Durasi Loading', 'Penyerahan Dokumen', 'Durasi Dokumen', 'No. Surat Jalan', 'Purchase Order'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerRange = 'A1:S1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 2;

        /** @var Checkpoint $cp */
        foreach ($data as $index => $cp) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cp->tanggal ? $cp->tanggal->format('d/m/Y') : '');
            $sheet->setCellValue('C' . $row, $cp->no_polisi);
            $sheet->setCellValue('D' . $row, $cp->vendor);
            $sheet->setCellValue('E' . $row, $cp->jenis_kendaraan);
            $sheet->setCellValue('F' . $row, $cp->jenis_barang);
            $sheet->setCellValue('G' . $row, $cp->aktivitas);
            $sheet->setCellValue('H' . $row, $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('I' . $row, $this->calculateWaitingTime($cp));
            $sheet->setCellValue('J' . $row, $cp->waktu_start ? $cp->waktu_start->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('K' . $row, $cp->waktu_end ? $cp->waktu_end->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('L' . $row, $this->formatGateLabel($cp->gate));
            $sheet->setCellValue('M' . $row, $cp->status);
            $sheet->setCellValue('N' . $row, $cp->status === 'CANCEL' ? $cp->cancel_note : $cp->note);
            $sheet->setCellValue('O' . $row, $cp->durasi);
            $sheet->setCellValue('P' . $row, $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('Q' . $row, $this->calculateDurasiDokumen($cp));
            $sheet->setCellValue('R' . $row, $cp->no_surat_jalan);
            $sheet->setCellValue('S' . $row, $cp->purchase_order);
            $row++;
        }

        // Data borders
        if ($row > 2) {
            $sheet->getStyle('A2:S' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'data_checkpoint_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Trigger: Penerimaan Dokumen (INBOUND - dokumen diterima)
     * Hanya mencatat waktu penerimaan dokumen
     */
    public function triggerPenerimaan(Request $request, Checkpoint $checkpoint)
    {
        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        if ($checkpoint->status === 'FINISH') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah selesai.");
        }

        $request->validate([
            'jenis_kendaraan' => 'required|string|max:255',
            'tipe'            => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_barang'    => 'required|in:FROZEN,DRY,CHILLED',
            'aktivitas'       => 'required|in:INBOUND,OUTBOUND',
            'no_surat_jalan'  => 'nullable|string|max:100',
            'purchase_order'  => 'nullable|string|max:100',
            'note'            => 'nullable|string|max:500',
        ]);

        $checkpoint->update([
            'jenis_kendaraan' => strtoupper($request->jenis_kendaraan),
            'tipe'            => $request->tipe,
            'jenis_barang'    => $request->jenis_barang,
            'aktivitas'       => $request->aktivitas,
            'no_surat_jalan'  => $request->no_surat_jalan,
            'purchase_order'  => $request->purchase_order,
            'note'            => $request->note,
            'waktu_penerimaan_dokumen' => \Carbon\Carbon::now(),
            'status'          => 'START',
        ]);

        return redirect()->back()
                         ->with('success', "Dokumen {$checkpoint->no_polisi} diterima dan status menjadi START.");
    }

    /**
     * Assign Gate
     * Menetapkan gate untuk kendaraan yang sudah diterima dokumennya
     */
    public function assignGate(Request $request, Checkpoint $checkpoint)
    {
        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        if ($checkpoint->status === 'FINISH') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah selesai.");
        }

        $request->validate([
            'gate' => 'required|integer|min:1|max:27',
        ]);

        $gateNumber = (int) $request->gate;

        // Check if gate is already in use today (has active loading)
        $gateInUse = Checkpoint::whereDate('created_at', Carbon::today())
            ->where('gate', $gateNumber)
            ->whereKeyNot($checkpoint->id)
            ->where('status', '!=', 'CANCEL')
            ->whereNotNull('waktu_penerimaan_dokumen')
            ->whereNull('waktu_end')
            ->exists();

        if ($gateInUse) {
            return redirect()->back()
                             ->with('error', "Gate {$gateNumber} sedang digunakan untuk loading.");
        }

        $checkpoint->update([
            'gate' => $gateNumber,
        ]);

        return redirect()->back()
                         ->with('success', "Gate {$gateNumber} ditetapkan untuk {$checkpoint->no_polisi}.");
    }

    /**
     * API: Get available gates (not currently used for active loading today)
     */
    public function getAvailableGates()
    {
        // Gates currently occupied: has gate assigned, penerimaan done, but loading not finished yet
        // $occupiedGates = Checkpoint::whereDate('created_at', Carbon::today())
            $occupiedGates = Checkpoint::whereNotNull('gate')
            ->whereNotNull('waktu_penerimaan_dokumen')
            ->where('status', '!=', 'CANCEL')
            ->whereNull('waktu_end')
            ->pluck('gate')
            ->unique()
            ->toArray();

        $allGates = [];
        for ($i = 1; $i <= 27; $i++) {
            $jenisBarang = $i <= 16 ? 'FROZEN' : 'DRY';
            $allGates[] = [
                'nomor' => $i,
                'jenis_barang' => $jenisBarang,
                'available' => !in_array($i, $occupiedGates),
            ];
        }

        return response()->json($allGates);
    }

    /**
     * Trigger: Penyerahan Dokumen (OUTBOUND - dokumen diserahkan)
     */
    public function triggerPenyerahan(Checkpoint $checkpoint)
    {
        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        $checkpoint->update([
            'waktu_penyerahan_dokumen' => Carbon::now(),
        ]);

        return redirect()->back()
                         ->with('success', "Waktu penyerahan dokumen {$checkpoint->no_polisi} dicatat.");
    }

    /**
     * Trigger: Start Loading
     */
    public function triggerStart(Checkpoint $checkpoint)
    {
        $currentUser = Auth::user();

        // Must have checkpoint.trigger permission
        if (!$currentUser->hasPermission('checkpoint.trigger') && !$currentUser->isAdmin()) {
            return redirect()->back()
                             ->with('error', 'Anda tidak memiliki izin untuk melakukan start loading.');
        }

        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        if ($checkpoint->status === 'FINISH') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah selesai.");
        }

        $checkpoint->update([
            'waktu_start' => Carbon::now(),
            'status' => 'ON LOADING',
            'started_by' => Auth::id(),
        ]);

        return redirect()->back()
                         ->with('success', "Loading {$checkpoint->no_polisi} dimulai.");
    }

    /**
     * Trigger: End Loading (auto-calculate durasi)
     * Validates that the user ending the loading is the same who started it (unless admin).
     */
    public function triggerEnd(Checkpoint $checkpoint)
    {
        $currentUser = Auth::user();

        // Must have checkpoint.trigger permission
        if (!$currentUser->hasPermission('checkpoint.trigger') && !$currentUser->isAdmin()) {
            return redirect()->back()
                             ->with('error', 'Anda tidak memiliki izin untuk melakukan end loading.');
        }

        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()
                             ->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        // Validate: user who ends must be the same who started
        // Exception: supervisor_admin with checkpoint.trigger, or administrator
        if ($checkpoint->started_by && $currentUser->id !== $checkpoint->started_by) {
            $isSupervisorWithTrigger = $currentUser->hasRole('supervisor_admin') && $currentUser->hasPermission('checkpoint.trigger');

            if (!$currentUser->isAdmin() && !$isSupervisorWithTrigger) {
                $starterName = $checkpoint->startedByUser?->name ?? 'Unknown';
                return redirect()->back()
                                 ->with('error', "Anda tidak dapat menyelesaikan loading ini. Loading dimulai oleh {$starterName}. Hanya user yang sama atau Supervisor yang dapat menyelesaikan loading.");
            }
        }

        $now = Carbon::now();
        $durasi = null;

        if ($checkpoint->waktu_start) {
            $diff = $checkpoint->waktu_start->diff($now);
            $hours = ($diff->days * 24) + $diff->h;
            $durasi = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
        }

        $checkpoint->update([
            'waktu_end' => $now,
            'status' => 'FINISH',
            'durasi' => $durasi,
        ]);

        return redirect()->back()
                         ->with('success', "Loading {$checkpoint->no_polisi} selesai. Durasi: {$durasi}");
    }

    /**
     * Cancel checkpoint data (Request or Direct)
     */
    public function cancel(Request $request, Checkpoint $checkpoint)
    {
        if ($checkpoint->status === 'CANCEL') {
            return redirect()->back()->with('error', "Checkpoint {$checkpoint->no_polisi} sudah dibatalkan.");
        }

        if ($checkpoint->status === 'FINISH') {
            return redirect()->back()->with('error', "Checkpoint {$checkpoint->no_polisi} sudah selesai dan tidak dapat dibatalkan.");
        }

        $validated = $request->validate([
            'cancel_note' => 'required|string|min:5|max:500',
        ], [
            'cancel_note.required' => 'Catatan cancel wajib diisi.',
            'cancel_note.min' => 'Catatan cancel minimal 5 karakter.',
            'cancel_note.max' => 'Catatan cancel maksimal 500 karakter.',
        ]);

        $user = Auth::user();

        // If user can approve cancel, they can direct cancel
        if ($user->hasPermission('checkpoint.approve_cancel') || $user->isAdmin()) {
            $checkpoint->update([
                'status' => 'CANCEL',
                'cancel_status' => 'approved',
                'cancel_reason' => $validated['cancel_note'],
                'cancel_note' => $validated['cancel_note'],
                'canceled_at' => Carbon::now(),
                'canceled_by' => $user->id,
                'cancel_requested_by' => $user->id,
                'cancel_approved_by' => $user->id,
            ]);
            return redirect()->back()->with('success', "Checkpoint {$checkpoint->no_polisi} berhasil dibatalkan secara langsung.");
        }

        // Otherwise (e.g. staff_admin), they can only request cancel
        $checkpoint->update([
            'cancel_status' => 'pending',
            'cancel_reason' => $validated['cancel_note'],
            'cancel_requested_by' => $user->id,
        ]);

        return redirect()->back()->with('success', "Request pembatalan untuk checkpoint {$checkpoint->no_polisi} berhasil dikirim dan menunggu approval.");
    }

    /**
     * Approve a cancel request
     */
    public function approveCancel(Checkpoint $checkpoint)
    {
        $user = Auth::user();

        if (!$user->hasPermission('checkpoint.approve_cancel') && !$user->isAdmin()) {
            abort(403);
        }

        if ($checkpoint->cancel_status !== 'pending') {
            return redirect()->back()->with('error', "Status tidak valid untuk di-approve.");
        }

        $checkpoint->update([
            'status' => 'CANCEL',
            'cancel_status' => 'approved',
            'cancel_note' => $checkpoint->cancel_reason,
            'canceled_at' => Carbon::now(),
            'canceled_by' => $user->id,
            'cancel_approved_by' => $user->id,
        ]);

        return redirect()->back()->with('success', "Request pembatalan disetujui, checkpoint telah dibatalkan.");
    }

    /**
     * Reject a cancel request
     */
    public function rejectCancel(Checkpoint $checkpoint)
    {
        $user = Auth::user();

        if (!$user->hasPermission('checkpoint.approve_cancel') && !$user->isAdmin()) {
            abort(403);
        }

        if ($checkpoint->cancel_status !== 'pending') {
            return redirect()->back()->with('error', "Status tidak valid untuk di-reject.");
        }

        $checkpoint->update([
            'cancel_status' => 'rejected',
        ]);

        return redirect()->back()->with('success', "Request pembatalan ditolak.");
    }

    /**
     * Import checkpoints from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $tanggal = trim($row[0] ?? '');
            $noPolisi = strtoupper(trim($row[1] ?? ''));
            $vendor = trim($row[2] ?? '');
            $driver = trim($row[3] ?? '');
            $tipe = strtoupper(trim($row[4] ?? ''));
            $jenisKendaraan = trim($row[5] ?? '');
            $jenisBarang = strtoupper(trim($row[6] ?? ''));
            $aktivitas = strtoupper(trim($row[7] ?? ''));
            $gate = trim($row[8] ?? '');

            if (empty($noPolisi) || empty($vendor) || empty($driver) || empty($jenisBarang) || empty($aktivitas)) {
                $skipped++;
                continue;
            }

            if (!in_array($jenisBarang, ['FROZEN', 'DRY']) || !in_array($aktivitas, ['INBOUND', 'OUTBOUND'])) {
                $skipped++;
                continue;
            }

            if (!in_array($tipe, ['INTERNAL', 'EKSTERNAL'])) {
                $tipe = 'EKSTERNAL';
            }

            try {
                $parsedDate = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
            } catch (\Exception $e) {
                $parsedDate = Carbon::today();
            }

            Checkpoint::create([
                'tanggal' => $parsedDate,
                'no_polisi' => $noPolisi,
                'vendor' => $vendor,
                'driver' => $driver,
                'tipe' => $tipe,
                'jenis_kendaraan' => $jenisKendaraan,
                'jenis_barang' => $jenisBarang,
                'aktivitas' => $aktivitas,
                'gate' => $gate ? (int)$gate : null,
                'status' => 'START',
            ]);
            $imported++;
        }

        $message = "{$imported} data checkpoint berhasil diimport.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return redirect()->route('checkpoints.index')->with('success', $message);
    }

    /**
     * Download import template for checkpoints
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Checkpoint');

        $headers = ['Tanggal (dd/mm/yyyy)', 'No Polisi', 'Vendor', 'Driver', 'Tipe (INTERNAL/EKSTERNAL)', 'Jenis Kendaraan', 'Jenis Barang (FROZEN/DRY)', 'Aktivitas (INBOUND/OUTBOUND)', 'Gate (Nomor)'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Sample data
        $sheet->setCellValue('A2', date('d/m/Y'));
        $sheet->setCellValue('B2', 'B 1234 ABC');
        $sheet->setCellValue('C2', 'PT. CONTOH VENDOR');
        $sheet->setCellValue('D2', 'NAMA DRIVER');
        $sheet->setCellValue('E2', 'EKSTERNAL');
        $sheet->setCellValue('F2', 'TRONTON');
        $sheet->setCellValue('G2', 'FROZEN');
        $sheet->setCellValue('H2', 'INBOUND');
        $sheet->setCellValue('I2', '1');

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_checkpoint.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function formatGateLabel($gate): string
    {
        if (!$gate) {
            return '-';
        }

        $gateNumber = (int) $gate;

        if ($gateNumber >= 1 && $gateNumber <= 16) {
            return 'F-' . $gateNumber;
        }

        if ($gateNumber >= 17 && $gateNumber <= 27) {
            return 'D-' . ($gateNumber - 16);
        }

        return 'Gate-' . $gate;
    }

    private function calculateDurasiDokumen(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->waktu_penerimaan_dokumen || !$checkpoint->waktu_penyerahan_dokumen) {
            return '';
        }

        $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;

        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }

    private function calculateWaitingTime(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->waktu_penerimaan_dokumen) {
            return '';
        }

        $diff = $checkpoint->created_at->diff($checkpoint->waktu_penerimaan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;

        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }
}
