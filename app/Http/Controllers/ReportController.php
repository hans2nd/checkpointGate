<?php

namespace App\Http\Controllers;

use App\Http\Traits\ReportQueryTrait;
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
    use ReportQueryTrait;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $aktivitas = $request->input('aktivitas', '');
        $jenisBarang = $request->input('jenis_barang', '');
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $query = $this->buildReportQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);

        $checkpoints = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Summary stats via trait
        $summary = $this->calculateSummary($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);
        $totalKendaraan = $summary['total_kendaraan'];
        $totalFinish = $summary['total_finish'];
        $totalCancel = $summary['total_cancel'];
        $totalOnLoading = $summary['total_on_loading'];
        $avgDurasi = $summary['avg_durasi_loading'];

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

        $data = $this->buildReportQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search)
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
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cp->tanggal ? $cp->tanggal->format('d/m/Y') : '');
            $sheet->setCellValue('C' . $row, $cp->no_polisi);
            $sheet->setCellValue('D' . $row, $cp->vendor);
            $sheet->setCellValue('E' . $row, $cp->driver);
            $sheet->setCellValue('F' . $row, $cp->tipe);
            $sheet->setCellValue('G' . $row, $cp->jenis_kendaraan);
            $sheet->setCellValue('H' . $row, $cp->jenis_barang);
            $sheet->setCellValue('I' . $row, $cp->aktivitas);
            $sheet->setCellValue('J' . $row, $this->formatGateLabel($cp->gate));
            $sheet->setCellValue('K' . $row, $cp->status);
            $sheet->setCellValue('L' . $row, $cp->created_at ? $cp->created_at->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('M' . $row, $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('N' . $row, $cp->waktu_start ? $cp->waktu_start->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('O' . $row, $cp->waktu_end ? $cp->waktu_end->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('P' . $row, $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('Q' . $row, $cp->waktu_keluar ? $cp->waktu_keluar->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('R' . $row, $this->calculateWaktuTunggu($cp));
            $sheet->setCellValue('S' . $row, $cp->durasi);
            $sheet->setCellValue('T' . $row, $this->calculateDurasiDokumen($cp));
            $sheet->setCellValue('U' . $row, $cp->canceled_at ? $cp->canceled_at->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('V' . $row, $cp->updated_at ? $cp->updated_at->format('d/m/Y H:i:s') : '');
            $sheet->setCellValue('W' . $row, $cp->no_surat_jalan);
            $sheet->setCellValue('X' . $row, $cp->purchase_order);
            $sheet->setCellValue('Y' . $row, $cp->receipt_number);
            $sheet->setCellValue('Z' . $row, optional($cp->createdByUser)->name);
            $sheet->setCellValue('AA' . $row, optional(optional($cp->createdByUser)->employee)->employee_id ?? optional($cp->createdByUser)->employee_id);
            $sheet->setCellValue('AB' . $row, optional($cp->receivedByUser)->name);
            $sheet->setCellValue('AC' . $row, optional(optional($cp->receivedByUser)->employee)->employee_id ?? optional($cp->receivedByUser)->employee_id);
            $sheet->setCellValue('AD' . $row, optional($cp->startedByUser)->name);
            $sheet->setCellValue('AE' . $row, optional(optional($cp->startedByUser)->employee)->employee_id ?? optional($cp->startedByUser)->employee_id);
            $sheet->setCellValue('AF' . $row, optional($cp->canceledByUser)->name);
            $sheet->setCellValue('AG' . $row, optional(optional($cp->canceledByUser)->employee)->employee_id ?? optional($cp->canceledByUser)->employee_id);
            $sheet->setCellValue('AH' . $row, $cp->note);
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
}
