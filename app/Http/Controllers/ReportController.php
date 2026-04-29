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

        $query = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status);

        $checkpoints = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->appends($request->query());

        // Summary stats
        $summaryQuery = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status);
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
            'avgDurasi'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $aktivitas = $request->input('aktivitas', '');
        $jenisBarang = $request->input('jenis_barang', '');
        $status = $request->input('status', '');

        $data = $this->buildQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Checkpoint');

        // Title row
        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'LAPORAN DATA CHECKPOINT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Info row
        $sheet->mergeCells('A2:R2');
        $periodLabel = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        $filterLabels = [];
        if ($aktivitas) $filterLabels[] = "Aktivitas: {$aktivitas}";
        if ($jenisBarang) $filterLabels[] = "Jenis: {$jenisBarang}";
        if ($status) $filterLabels[] = "Status: {$status}";
        $filterInfo = $filterLabels ? ' | ' . implode(', ', $filterLabels) : '';
        $sheet->setCellValue('A2', "Periode: {$periodLabel}{$filterInfo} | Total: {$data->count()} kendaraan");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Headers
        $headers = ['No', 'Tanggal', 'No Polisi', 'Vendor', 'Kendaraan', 'Barang', 'Aktivitas', 'Penerimaan Dokumen', 'Start Loading', 'End Loading', 'Gate', 'Status', 'Catatan', 'Durasi Loading', 'Penyerahan Dokumen', 'Durasi Dokumen', 'No. Surat Jalan', 'Purchase Order'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '4';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A4:R4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 5;
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
            $sheet->setCellValue('I' . $row, $cp->waktu_start ? $cp->waktu_start->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('J' . $row, $cp->waktu_end ? $cp->waktu_end->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('K' . $row, $this->formatGateLabel($cp->gate));
            $sheet->setCellValue('L' . $row, $cp->status);
            $sheet->setCellValue('M' . $row, $cp->status === 'CANCEL' ? $cp->cancel_note : $cp->note);
            $sheet->setCellValue('N' . $row, $cp->durasi);
            $sheet->setCellValue('O' . $row, $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('P' . $row, $this->calculateDurasiDokumen($cp));
            $sheet->setCellValue('Q' . $row, $cp->no_surat_jalan);
            $sheet->setCellValue('R' . $row, $cp->purchase_order);
            $row++;
        }

        // Data borders
        if ($row > 5) {
            $sheet->getStyle('A5:R' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'font' => ['size' => 10],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'report_checkpoint_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function buildQuery(string $startDate, string $endDate, ?string $aktivitas, ?string $jenisBarang, ?string $status)
    {
        $parsedStart = Carbon::parse($startDate)->startOfDay();
        $parsedEnd = Carbon::parse($endDate)->endOfDay();
        $dayBeforeStart = Carbon::parse($startDate)->subDay();

        $query = Checkpoint::where(function ($dateScope) use ($parsedStart, $parsedEnd, $dayBeforeStart) {
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
        if ($aktivitas) $query->where('aktivitas', $aktivitas);
        if ($jenisBarang) $query->where('jenis_barang', $jenisBarang);
        if ($status) $query->where('status', $status);

        return $query;
    }

    private function formatGateLabel($gate): string
    {
        if (!$gate) return '-';
        $gateNumber = (int) $gate;
        if ($gateNumber >= 1 && $gateNumber <= 16) return 'F-' . $gateNumber;
        if ($gateNumber >= 17 && $gateNumber <= 27) return 'D-' . ($gateNumber - 16);
        return 'Gate-' . $gate;
    }

    private function calculateDurasiDokumen(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->waktu_penerimaan_dokumen || !$checkpoint->waktu_penyerahan_dokumen) return '';
        $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;
        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }
}
