<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Gate;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
            'jenis_barang' => 'required|in:FROZEN,DRY',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
            'gate' => 'nullable|integer',
        ]);

        $validated['tanggal'] = Carbon::today();
        $validated['status'] = 'START';

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
            'waktu_penerimaan_dokumen' => 'nullable|date',
            'waktu_penyerahan_dokumen' => 'nullable|date',
            'jenis_barang' => 'required|in:FROZEN,DRY',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
            'gate' => 'nullable|integer',
            'status' => 'required|in:START,FINISH',
            'waktu_start' => 'nullable|date',
            'waktu_end' => 'nullable|date',
            'durasi' => 'nullable|string|max:20',
        ]);

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

        $data = $query->orderBy('tanggal', 'desc')
                      ->orderBy('waktu_penerimaan_dokumen', 'desc')
                      ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Checkpoint');

        // Header
        $headers = ['No', 'Tanggal', 'No Polisi', 'Vendor', 'Driver', 'Tipe', 'Jenis Kendaraan', 'Jenis Barang', 'Aktivitas', 'Gate', 'Penerimaan Dokumen', 'Penyerahan Dokumen', 'Durasi Dokumen', 'Waktu Start', 'Waktu End', 'Status', 'Durasi Loading'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerRange = 'A1:Q1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 2;
        foreach ($data as $index => $cp) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cp->tanggal ? $cp->tanggal->format('d/m/Y') : '');
            $sheet->setCellValue('C' . $row, $cp->no_polisi);
            $sheet->setCellValue('D' . $row, $cp->vendor);
            $sheet->setCellValue('E' . $row, $cp->driver);
            $sheet->setCellValue('F' . $row, $cp->tipe);
            $sheet->setCellValue('G' . $row, $cp->jenis_kendaraan);
            $sheet->setCellValue('H' . $row, $cp->jenis_barang);
            $sheet->setCellValue('I' . $row, $cp->aktivitas);
            $sheet->setCellValue('J' . $row, $cp->gate);
            $sheet->setCellValue('K' . $row, $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('L' . $row, $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('d/m/Y H:i:s') : '');
            // Durasi Dokumen (penerimaan -> penyerahan)
            $durasiDokumen = '';
            if ($cp->waktu_penerimaan_dokumen && $cp->waktu_penyerahan_dokumen) {
                $diff = $cp->waktu_penerimaan_dokumen->diff($cp->waktu_penyerahan_dokumen);
                $hours = ($diff->days * 24) + $diff->h;
                $durasiDokumen = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
            }
            $sheet->setCellValue('M' . $row, $durasiDokumen);
            $sheet->setCellValue('N' . $row, $cp->waktu_start ? $cp->waktu_start->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('O' . $row, $cp->waktu_end ? $cp->waktu_end->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('P' . $row, $cp->status);
            $sheet->setCellValue('Q' . $row, $cp->durasi);
            $row++;
        }

        // Data borders
        if ($row > 2) {
            $sheet->getStyle('A2:Q' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
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
     * Now also assigns gate number
     */
    public function triggerPenerimaan(Request $request, Checkpoint $checkpoint)
    {
        $request->validate([
            'gate' => 'required|integer|min:1|max:27',
        ]);

        $gateNumber = (int) $request->gate;

        // Check if gate is already in use today (has active loading)
        $gateInUse = Checkpoint::whereDate('tanggal', Carbon::today())
            ->where('gate', $gateNumber)
            ->where('status', 'START')
            ->whereNotNull('waktu_start')
            ->whereNull('waktu_end')
            ->exists();

        if ($gateInUse) {
            return redirect()->back()
                             ->with('error', "Gate {$gateNumber} sedang digunakan untuk loading.");
        }

        $checkpoint->update([
            'waktu_penerimaan_dokumen' => Carbon::now(),
            'gate' => $gateNumber,
        ]);

        return redirect()->back()
                         ->with('success', "Dokumen {$checkpoint->no_polisi} diterima. Gate {$gateNumber} ditetapkan.");
    }

    /**
     * API: Get available gates (not currently used for active loading today)
     */
    public function getAvailableGates()
    {
        // Gates currently occupied: has gate assigned, penerimaan done, but loading not finished yet
        $occupiedGates = Checkpoint::whereDate('tanggal', Carbon::today())
            ->whereNotNull('gate')
            ->whereNotNull('waktu_penerimaan_dokumen')
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
        $checkpoint->update([
            'waktu_start' => Carbon::now(),
            'status' => 'ON LOADING',
        ]);

        return redirect()->back()
                         ->with('success', "Loading {$checkpoint->no_polisi} dimulai.");
    }

    /**
     * Trigger: End Loading (auto-calculate durasi)
     */
    public function triggerEnd(Checkpoint $checkpoint)
    {
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
}
