<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GateController extends Controller
{
    public function index(Request $request)
    {
        $query = Gate::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_gate', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($aktivitas = $request->input('aktivitas')) {
            $query->where('aktivitas', $aktivitas);
        }

        if ($kendaraan = $request->input('jenis_kendaraan')) {
            $query->where('jenis_kendaraan', $kendaraan);
        }

        $perPage = in_array($request->input('per_page'), [10,15,25,50,100,1000]) ? (int)$request->input('per_page') : 15;

        $gates = $query->orderBy('nomor_gate')
                       ->orderBy('jenis_barang')
                       ->orderBy('jenis_kendaraan')
                       ->orderBy('aktivitas')
                       ->paginate($perPage)
                       ->withQueryString();

        $jenisKendaraanList = Gate::distinct()->pluck('jenis_kendaraan')->sort()->values();

        return view('gates.index', compact('gates', 'jenisKendaraanList'));
    }

    public function create()
    {
        $jenisKendaraanList = Gate::distinct()->pluck('jenis_kendaraan')->sort()->values();
        return view('gates.create', compact('jenisKendaraanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_gate' => 'required|integer|min:1',
            'jenis_barang' => 'required|in:FROZEN,DRY',
            'jenis_kendaraan' => 'required|string|max:50',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
        ]);

        Gate::create($validated);

        return redirect()->route('gates.index')
                         ->with('success', 'Data gate berhasil ditambahkan.');
    }

    public function edit(Gate $gate)
    {
        $jenisKendaraanList = Gate::distinct()->pluck('jenis_kendaraan')->sort()->values();
        return view('gates.edit', compact('gate', 'jenisKendaraanList'));
    }

    public function update(Request $request, Gate $gate)
    {
        $validated = $request->validate([
            'nomor_gate' => 'required|integer|min:1',
            'jenis_barang' => 'required|in:FROZEN,DRY',
            'jenis_kendaraan' => 'required|string|max:50',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
        ]);

        $gate->update($validated);

        return redirect()->route('gates.index')
                         ->with('success', 'Data gate berhasil diperbarui.');
    }

    public function destroy(Gate $gate)
    {
        $gate->delete();

        return redirect()->route('gates.index')
                         ->with('success', 'Data gate berhasil dihapus.');
    }

    /**
     * Bulk delete gates
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        Gate::whereIn('id', $request->ids)->delete();

        return redirect()->route('gates.index')
                         ->with('success', count($request->ids) . ' data gate berhasil dihapus.');
    }

    /**
     * Export gates to Excel
     */
    public function export(Request $request)
    {
        $query = Gate::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_gate', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($aktivitas = $request->input('aktivitas')) {
            $query->where('aktivitas', $aktivitas);
        }

        if ($kendaraan = $request->input('jenis_kendaraan')) {
            $query->where('jenis_kendaraan', $kendaraan);
        }

        $data = $query->orderBy('nomor_gate')
                      ->orderBy('jenis_barang')
                      ->orderBy('jenis_kendaraan')
                      ->orderBy('aktivitas')
                      ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Gate');

        $headers = ['No', 'Nomor Gate', 'Jenis Barang', 'Jenis Kendaraan', 'Aktivitas'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 2;
        foreach ($data as $index => $gate) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $gate->nomor_gate);
            $sheet->setCellValue('C' . $row, $gate->jenis_barang);
            $sheet->setCellValue('D' . $row, $gate->jenis_kendaraan);
            $sheet->setCellValue('E' . $row, $gate->aktivitas);
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('A2:E' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'master_gate_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import gates from Excel
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

            $nomorGate = trim($row[0] ?? '');
            $jenisBarang = strtoupper(trim($row[1] ?? ''));
            $jenisKendaraan = trim($row[2] ?? '');
            $aktivitas = strtoupper(trim($row[3] ?? ''));

            if (empty($nomorGate) || empty($jenisBarang) || empty($jenisKendaraan) || empty($aktivitas)) {
                $skipped++;
                continue;
            }

            if (!in_array($jenisBarang, ['FROZEN', 'DRY']) || !in_array($aktivitas, ['INBOUND', 'OUTBOUND'])) {
                $skipped++;
                continue;
            }

            Gate::create([
                'nomor_gate' => (int) $nomorGate,
                'jenis_barang' => $jenisBarang,
                'jenis_kendaraan' => $jenisKendaraan,
                'aktivitas' => $aktivitas,
            ]);
            $imported++;
        }

        $message = "{$imported} data gate berhasil diimport.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return redirect()->route('gates.index')->with('success', $message);
    }

    /**
     * Download import template for gates
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Gate');

        $headers = ['Nomor Gate', 'Jenis Barang (FROZEN/DRY)', 'Jenis Kendaraan', 'Aktivitas (INBOUND/OUTBOUND)'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'FROZEN');
        $sheet->setCellValue('C2', 'TRONTON');
        $sheet->setCellValue('D2', 'INBOUND');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_gate.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
