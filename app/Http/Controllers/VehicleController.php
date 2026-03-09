<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                  ->orWhere('driver', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($vendor = $request->input('vendor')) {
            $query->where('vendor', $vendor);
        }

        $perPage = in_array($request->input('per_page'), [10,15,25,50,100,1000]) ? (int)$request->input('per_page') : 15;

        $vehicles = $query->orderBy('no_polisi')
                          ->paginate($perPage)
                          ->withQueryString();

        $vendorList = Vehicle::distinct()->pluck('vendor')->sort()->values();

        return view('vehicles.index', compact('vehicles', 'vendorList'));
    }

    public function create()
    {
        $jenisKendaraanList = Vehicle::JENIS_KENDARAAN;
        return view('vehicles.create', compact('jenisKendaraanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|string|max:20|unique:vehicles,no_polisi',
            'driver' => 'required|string|max:100',
            'vendor' => 'required|string|max:100',
            'tipe' => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'required|in:' . implode(',', Vehicle::JENIS_KENDARAAN),
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')
                         ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        $jenisKendaraanList = Vehicle::JENIS_KENDARAAN;
        return view('vehicles.edit', compact('vehicle', 'jenisKendaraanList'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|string|max:20|unique:vehicles,no_polisi,' . $vehicle->id,
            'driver' => 'required|string|max:100',
            'vendor' => 'required|string|max:100',
            'tipe' => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'required|in:' . implode(',', Vehicle::JENIS_KENDARAAN),
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
                         ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
                         ->with('success', 'Data kendaraan berhasil dihapus.');
    }

    /**
     * Bulk delete vehicles
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        Vehicle::whereIn('id', $request->ids)->delete();

        return redirect()->route('vehicles.index')
                         ->with('success', count($request->ids) . ' data kendaraan berhasil dihapus.');
    }

    /**
     * Export vehicles to Excel
     */
    public function export(Request $request)
    {
        $query = Vehicle::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                  ->orWhere('driver', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($vendor = $request->input('vendor')) {
            $query->where('vendor', $vendor);
        }

        $data = $query->orderBy('no_polisi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Kendaraan');

        $headers = ['No', 'No Polisi', 'Driver', 'Vendor', 'Tipe', 'Jenis Kendaraan'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 2;
        foreach ($data as $index => $v) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $v->no_polisi);
            $sheet->setCellValue('C' . $row, $v->driver);
            $sheet->setCellValue('D' . $row, $v->vendor);
            $sheet->setCellValue('E' . $row, $v->tipe);
            $sheet->setCellValue('F' . $row, $v->jenis_kendaraan);
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('A2:F' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'master_kendaraan_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import vehicles from Excel
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
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $validJenis = Vehicle::JENIS_KENDARAAN;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header
            $rowNum = $index + 1; // Nomor baris Excel (1-based, termasuk header)

            $noPolisi = strtoupper(trim($row[0] ?? ''));
            $driver = trim($row[1] ?? '');
            $vendor = trim($row[2] ?? '');
            $tipe = strtoupper(trim($row[3] ?? ''));
            $rawJenis = trim($row[4] ?? '');

            // Normalisasi jenis kendaraan: uppercase, spasi → strip
            $jenisKendaraan = strtoupper(str_replace(' ', '-', $rawJenis));

            if (empty($noPolisi) || empty($driver) || empty($vendor) || empty($tipe) || empty($jenisKendaraan)) {
                $skipped++;
                continue;
            }

            if (!in_array($tipe, ['INTERNAL', 'EKSTERNAL'])) {
                $errors[] = "Baris {$rowNum}: Tipe '{$row[3]}' tidak valid (harus INTERNAL/EKSTERNAL).";
                $skipped++;
                continue;
            }

            if (!in_array($jenisKendaraan, $validJenis)) {
                $errors[] = "Baris {$rowNum}: Jenis kendaraan '{$rawJenis}' tidak valid. Nilai yang diizinkan: " . implode(', ', $validJenis) . ".";
                $skipped++;
                continue;
            }

            $existing = Vehicle::where('no_polisi', $noPolisi)->first();
            if ($existing) {
                $existing->update([
                    'driver' => $driver,
                    'vendor' => $vendor,
                    'tipe' => $tipe,
                    'jenis_kendaraan' => $jenisKendaraan,
                ]);
                $updated++;
            } else {
                Vehicle::create([
                    'no_polisi' => $noPolisi,
                    'driver' => $driver,
                    'vendor' => $vendor,
                    'tipe' => $tipe,
                    'jenis_kendaraan' => $jenisKendaraan,
                ]);
                $imported++;
            }
        }

        // Build result message
        if (!empty($errors)) {
            $errorMsg = implode(' | ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $errorMsg .= ' | ...dan ' . (count($errors) - 5) . ' error lainnya.';
            }
            $successPart = "{$imported} diimport, {$updated} diupdate, {$skipped} dilewati.";
            return redirect()->route('vehicles.index')
                ->with('warning', $successPart . ' Detail error: ' . $errorMsg);
        }

        $message = "{$imported} data kendaraan baru diimport.";
        if ($updated > 0) {
            $message .= " {$updated} data diupdate.";
        }
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return redirect()->route('vehicles.index')->with('success', $message);
    }

    /**
     * API: Get vehicle data by no_polisi for auto-fill
     */
    public function getByNoPolisi(Request $request)
    {
        $vehicle = Vehicle::where('no_polisi', $request->input('no_polisi'))->first();

        if ($vehicle) {
            return response()->json([
                'found' => true,
                'data' => $vehicle,
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * API: Search vehicles for autocomplete
     */
    public function search(Request $request)
    {
        $term = $request->input('term', '');

        $vehicles = Vehicle::where('no_polisi', 'like', "%{$term}%")
                          ->orderBy('no_polisi')
                          ->limit(10)
                          ->get(['id', 'no_polisi', 'driver', 'vendor', 'tipe', 'jenis_kendaraan']);

        return response()->json($vehicles);
    }

    /**
     * Download import template for vehicles
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kendaraan');

        $headers = ['No Polisi', 'Driver', 'Vendor', 'Tipe (INTERNAL/EKSTERNAL)', 'Jenis Kendaraan'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->setCellValue('A2', 'B 1234 ABC');
        $sheet->setCellValue('B2', 'NAMA DRIVER');
        $sheet->setCellValue('C2', 'PT. CONTOH VENDOR');
        $sheet->setCellValue('D2', 'EKSTERNAL');
        $sheet->setCellValue('E2', 'TRONTON');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet referensi: daftar jenis kendaraan yang valid
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Jenis Kendaraan');

        $refSheet->setCellValue('A1', 'Jenis Kendaraan yang Valid');
        $refSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (Vehicle::JENIS_KENDARAAN as $idx => $jk) {
            $refSheet->setCellValue('A' . ($idx + 2), $jk);
        }

        $lastRef = count(Vehicle::JENIS_KENDARAAN) + 1;
        $refSheet->getStyle('A2:A' . $lastRef)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $refSheet->getColumnDimension('A')->setAutoSize(true);

        // Kembali ke sheet pertama sebagai aktif
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'template_kendaraan.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
