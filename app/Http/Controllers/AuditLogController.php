<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AuditLogController extends Controller
{
    /**
     * Display audit log list with filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by module
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }

        // Filter by channel
        if ($channel = $request->input('channel')) {
            $query->where('channel', $channel);
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $perPage = in_array($request->input('per_page'), [15, 25, 50, 100]) ? (int) $request->input('per_page') : 25;

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Distinct values for filter dropdowns
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('audit-logs.index', compact('logs', 'actions', 'modules'));
    }

    /**
     * Export audit logs to Excel.
     */
    public function export(Request $request)
    {
        $query = AuditLog::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }
        if ($channel = $request->input('channel')) {
            $query->where('channel', $channel);
        }
        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Audit Logs');

        // Headers
        $headers = ['No', 'Waktu', 'User', 'Email', 'Action', 'Module', 'Deskripsi', 'IP Address', 'Channel', 'Old Values', 'New Values'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerRange = 'A1:K1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        $row = 2;
        foreach ($data as $index => $log) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $log->created_at?->format('d/m/Y H:i:s'));
            $sheet->setCellValue('C' . $row, $log->user_name);
            $sheet->setCellValue('D' . $row, $log->user_email);
            $sheet->setCellValue('E' . $row, $log->action);
            $sheet->setCellValue('F' . $row, $log->module);
            $sheet->setCellValue('G' . $row, $log->description);
            $sheet->setCellValue('H' . $row, $log->ip_address);
            $sheet->setCellValue('I' . $row, $log->channel);
            $sheet->setCellValue('J' . $row, $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '');
            $sheet->setCellValue('K' . $row, $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '');
            $row++;
        }

        // Data borders
        if ($row > 2) {
            $sheet->getStyle('A2:K' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'audit_log_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Purge audit logs by date range.
     */
    public function purge(Request $request)
    {
        $request->validate([
            'purge_date_from' => 'required|date',
            'purge_date_to' => 'required|date|after_or_equal:purge_date_from',
        ], [
            'purge_date_from.required' => 'Tanggal mulai wajib diisi.',
            'purge_date_to.required' => 'Tanggal akhir wajib diisi.',
            'purge_date_to.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal mulai.',
        ]);

        $from = Carbon::parse($request->purge_date_from)->startOfDay();
        $to = Carbon::parse($request->purge_date_to)->endOfDay();

        $count = AuditLog::whereBetween('created_at', [$from, $to])->count();
        AuditLog::whereBetween('created_at', [$from, $to])->delete();

        // Log the purge action itself
        \App\Services\AuditLogService::log(
            'purge', 'audit_log',
            "{$count} audit log dihapus (range: {$request->purge_date_from} s/d {$request->purge_date_to})",
        );

        return redirect()->route('audit-logs.index')
            ->with('success', "{$count} data audit log berhasil dihapus.");
    }
}
