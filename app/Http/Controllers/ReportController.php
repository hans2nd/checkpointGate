<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $aktivitas = $request->input('aktivitas', '');
        $jenisBarang = $request->input('jenis_barang', '');
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $query = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);

        $checkpoints = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Summary stats
        $summaryQuery = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);
        $totalKendaraan = (clone $summaryQuery)->count();
        $totalFinish = (clone $summaryQuery)->where('status', 'FINISH')->count();
        $totalCancel = (clone $summaryQuery)->where('status', 'CANCEL')->count();
        $totalOnLoading = (clone $summaryQuery)->where('status', 'ON LOADING')->count();

        // Average durasi loading (only FINISH with durasi)
        $avgDurasi = null;
        $durasiList = (clone $summaryQuery)->where('status', 'FINISH')
            ->whereNotNull('durasi')
            ->pluck('durasi');
        if ($durasiList->isNotEmpty()) {
            $totalSeconds = 0;
            $count = 0;
            foreach ($durasiList as $d) {
                $parts = explode(':', $d);
                if (count($parts) === 3) {
                    $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                    $count++;
                }
            }
            if ($count > 0) {
                $avg = intval($totalSeconds / $count);
                $avgDurasi = sprintf('%02d:%02d:%02d', intdiv($avg, 3600), intdiv($avg % 3600, 60), $avg % 60);
            }
        }

        return view('report.index', compact(
            'checkpoints',
            'startDate',
            'endDate',
            'aktivitas',
            'jenisBarang',
            'status',
            'totalKendaraan',
            'totalFinish',
            'totalCancel',
            'totalOnLoading',
            'avgDurasi',
            'search'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $aktivitas = $request->input('aktivitas', '');
        $jenisBarang = $request->input('jenis_barang', '');
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $data = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Report Checkpoint'));

        // Title row
        $sheet->mergeCells('A1:AH1');
        $sheet->setCellValue('A1', __('LAPORAN DATA CHECKPOINT'));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Info row
        $sheet->mergeCells('A2:AH2');
        $periodLabel = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        $filterLabels = [];
        if ($aktivitas)
            $filterLabels[] = __('Aktivitas') . ": {$aktivitas}";
        if ($jenisBarang)
            $filterLabels[] = __('Jenis') . ": {$jenisBarang}";
        if ($status)
            $filterLabels[] = __('Status') . ": {$status}";
        if ($search)
            $filterLabels[] = __('Pencarian') . ": {$search}";
        $filterInfo = $filterLabels ? ' | ' . implode(', ', $filterLabels) : '';
        $sheet->setCellValue('A2', __('Periode') . ": {$periodLabel}{$filterInfo} | " . __('Total') . ": {$data->count()} " . __('kendaraan'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Headers
        $headers = [
            __('No'),
            __('Tanggal'),
            __('No Polisi'),
            __('Vendor'),
            __('Driver'),
            __('Tipe'),
            __('Jenis Kendaraan'),
            __('Jenis Barang'),
            __('Aktivitas'),
            __('No. Surat Jalan'),
            __('Purchase Order'),
            __('Receipt Number'),
            __('Gate'),
            __('Status'),
            __('Waktu Tunggu'),
            __('Waktu Penerimaan Dokumen'),
            __('Waktu Penyerahan Dokumen'),
            __('Waktu Keluar'),
            __('Durasi Dokumen'),
            __('Waktu Start Loading'),
            __('Waktu End Loading'),
            __('Durasi Loading/Unloading'),
            __('Dibuat Oleh'),
            __('Employee ID') . ' (' . __('Dibuat Oleh') . ')',
            __('Diterima Oleh'),
            __('Employee ID') . ' (' . __('Diterima Oleh') . ')',
            __('Start Loading Oleh'),
            __('Employee ID') . ' (' . __('Start Loading Oleh') . ')',
            __('Cancel Oleh'),
            __('Employee ID') . ' (' . __('Cancel Oleh') . ')',
            __('Waktu Cancel'),
            __('Note'),
            __('Dibuat'),
            __('Diperbarui')
        ];

        foreach ($headers as $col => $header) {
            // Converts 0-25 to A-Z, 26-51 to AA-AZ, etc.
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $cell = $colLetter . '4';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A4:AH4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 5;
        /** @var Checkpoint $cp */
        foreach ($data as $index => $cp) {
            $waktuTunggu = '';
            if ($cp->created_at && $cp->waktu_penerimaan_dokumen) {
                $diffTunggu = $cp->created_at->diff($cp->waktu_penerimaan_dokumen);
                $hoursTunggu = $diffTunggu->days * 24 + $diffTunggu->h;
                $waktuTunggu = sprintf('%02d:%02d:%02d', $hoursTunggu, $diffTunggu->i, $diffTunggu->s);
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cp->tanggal ? $cp->tanggal->format('d/m/Y') : '');
            $sheet->setCellValue('C' . $row, $cp->no_polisi);
            $sheet->setCellValue('D' . $row, $cp->vendor);
            $sheet->setCellValue('E' . $row, $cp->driver);
            $sheet->setCellValue('F' . $row, $cp->tipe);
            $sheet->setCellValue('G' . $row, $cp->jenis_kendaraan);
            $sheet->setCellValue('H' . $row, $cp->jenis_barang);
            $sheet->setCellValue('I' . $row, $cp->aktivitas);
            $sheet->setCellValue('J' . $row, $cp->no_surat_jalan);
            $sheet->setCellValue('K' . $row, $cp->purchase_order);
            $sheet->setCellValue('L' . $row, $cp->receipt_number);
            $sheet->setCellValue('M' . $row, $this->formatGateLabel($cp->gate));
            $sheet->setCellValue('N' . $row, $cp->status);
            $sheet->setCellValue('O' . $row, $waktuTunggu);
            $sheet->setCellValue('P' . $row, $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('Q' . $row, $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('R' . $row, $cp->waktu_keluar ? $cp->waktu_keluar->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('S' . $row, $this->calculateDurasiDokumen($cp));
            $sheet->setCellValue('T' . $row, $cp->waktu_start ? $cp->waktu_start->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('U' . $row, $cp->waktu_end ? $cp->waktu_end->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('V' . $row, $cp->durasi);
            $sheet->setCellValue('W' . $row, optional($cp->createdByUser)->name);
            $sheet->setCellValue('X' . $row, optional(optional($cp->createdByUser)->employee)->employee_id ?? optional($cp->createdByUser)->employee_id);
            $sheet->setCellValue('Y' . $row, optional($cp->receivedByUser)->name);
            $sheet->setCellValue('Z' . $row, optional(optional($cp->receivedByUser)->employee)->employee_id ?? optional($cp->receivedByUser)->employee_id);
            $sheet->setCellValue('AA' . $row, optional($cp->startedByUser)->name);
            $sheet->setCellValue('AB' . $row, optional(optional($cp->startedByUser)->employee)->employee_id ?? optional($cp->startedByUser)->employee_id);
            $sheet->setCellValue('AC' . $row, optional($cp->canceledByUser)->name);
            $sheet->setCellValue('AD' . $row, optional(optional($cp->canceledByUser)->employee)->employee_id ?? optional($cp->canceledByUser)->employee_id);
            $sheet->setCellValue('AE' . $row, $cp->canceled_at ? $cp->canceled_at->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('AF' . $row, $cp->note);
            $sheet->setCellValue('AG' . $row, $cp->created_at ? $cp->created_at->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('AH' . $row, $cp->updated_at ? $cp->updated_at->format('d/m/Y H:i:s') : '');
            $row++;
        }

        // Data borders
        if ($row > 5) {
            $sheet->getStyle('A5:AH' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'font' => ['size' => 10],
            ]);
        }

        // Auto-size columns
        foreach (range(1, 34) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $filename = 'report_checkpoint_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function buildQuery(string $startDate, string $endDate, ?string $aktivitas, ?string $jenisBarang, ?string $status, ?string $search = null)
    {
        $parsedStart = Carbon::parse($startDate)->startOfDay();
        $parsedEnd = Carbon::parse($endDate)->endOfDay();
        $dayBeforeStart = Carbon::parse($startDate)->subDay();

        $query = Checkpoint::with(['createdByUser', 'receivedByUser', 'startedByUser', 'canceledByUser'])->where(function ($dateScope) use ($parsedStart, $parsedEnd, $dayBeforeStart) {
            // Main date range
            $dateScope->whereBetween('tanggal', [$parsedStart, $parsedEnd])
                // Overnight: started day before but still active or finished after midnight
                ->orWhere(function ($overnight) use ($dayBeforeStart, $parsedStart) {
                    $overnight->whereDate('tanggal', $dayBeforeStart)
                        ->where('status', '!=', 'CANCEL')
                        ->where(function ($inner) use ($parsedStart) {
                            $inner->where('status', 'ON LOADING')
                                ->orWhere(function ($fin) use ($parsedStart) {
                                    $fin->where('status', 'FINISH')
                                        ->whereNotNull('waktu_end')
                                        ->where('waktu_end', '>=', $parsedStart);
                                });
                        });
                });
        });

        // Apply filters OUTSIDE the date scope so they apply to ALL results
        if ($aktivitas)
            $query->where('aktivitas', $aktivitas);
        if ($jenisBarang)
            $query->where('jenis_barang', $jenisBarang);
        if ($status)
            $query->where('status', $status);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('purchase_order', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function formatGateLabel($gate): string
    {
        if (!$gate)
            return '-';
        $gateNumber = (int) $gate;
        if ($gateNumber >= 1 && $gateNumber <= 16)
            return 'F-' . $gateNumber;
        if ($gateNumber >= 17 && $gateNumber <= 27)
            return 'D-' . ($gateNumber - 16);
        return 'Gate-' . $gate;
    }

    private function calculateDurasiDokumen(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->waktu_penerimaan_dokumen || !$checkpoint->waktu_penyerahan_dokumen)
            return '';
        $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;
        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }
}
