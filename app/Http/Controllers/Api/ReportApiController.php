<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ReportQueryTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * API Controller for PowerBI Reporting.
 *
 * Provides JSON endpoints with the same 34 columns as the Excel export.
 * Uses chunked streaming for large datasets to maintain performance.
 *
 * Endpoints:
 *   GET /api/report         — Full report data (streamed JSON)
 *   GET /api/report/summary — Summary statistics only
 */
class ReportApiController extends Controller
{
    use ReportQueryTrait;

    /**
     * GET /api/report
     *
     * Returns all checkpoint data for the given date range as a streamed JSON response.
     * Each row contains 34 columns matching the Excel export.
     *
     * Query parameters:
     *  - start_date    (Y-m-d, default: start of current month)
     *  - end_date      (Y-m-d, default: today)
     *  - aktivitas     (INBOUND|OUTBOUND)
     *  - jenis_barang  (FROZEN|DRY|CHILLED)
     *  - status        (PARKING|START|ON LOADING|FINISH|CANCEL|COMPLETED)
     *  - search        (searches: no_polisi, vendor, no_surat_jalan, purchase_order, note)
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $aktivitas = $request->input('aktivitas');
        $jenisBarang = $request->input('jenis_barang');
        $status = $request->input('status');
        $search = $request->input('search');

        // Build base query
        $query = $this->buildReportQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);

        // Stream the JSON response using chunked queries for memory efficiency
        return response()->stream(function () use ($query) {
            // Open the JSON array
            echo '[';

            $counter = 0;
            $chunkSize = 500;

            $query->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->chunk($chunkSize, function ($checkpoints) use (&$counter) {
                    foreach ($checkpoints as $cp) {
                        if ($counter > 0) {
                            echo ',';
                        }

                        $row = [
                            'no' => $counter + 1,
                            'tanggal' => $cp->tanggal ? $cp->tanggal->format('Y-m-d') : null,
                            'no_polisi' => $cp->no_polisi,
                            'vendor' => $cp->vendor,
                            'driver' => $cp->driver,
                            'tipe' => $cp->tipe,
                            'jenis_kendaraan' => $cp->jenis_kendaraan,
                            'jenis_barang' => $cp->jenis_barang,
                            'aktivitas' => $cp->aktivitas,
                            'no_surat_jalan' => $cp->no_surat_jalan,
                            'purchase_order' => $cp->purchase_order,
                            'receipt_number' => $cp->receipt_number,
                            'gate' => $this->formatGateLabel($cp->gate),
                            'status' => $cp->status,
                            'waktu_tunggu' => $this->calculateWaktuTunggu($cp) ?: null,
                            'waktu_penerimaan_dokumen' => $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->toIso8601String() : null,
                            'waktu_penyerahan_dokumen' => $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->toIso8601String() : null,
                            'waktu_keluar' => $cp->waktu_keluar ? $cp->waktu_keluar->toIso8601String() : null,
                            'durasi_dokumen' => $this->calculateDurasiDokumen($cp) ?: null,
                            'waktu_start_loading' => $cp->waktu_start ? $cp->waktu_start->toIso8601String() : null,
                            'waktu_end_loading' => $cp->waktu_end ? $cp->waktu_end->toIso8601String() : null,
                            'durasi_loading' => $cp->durasi ?: null,
                            'dibuat_oleh' => optional($cp->createdByUser)->name,
                            'employee_id_dibuat_oleh' => optional(optional($cp->createdByUser)->employee)->employee_id ?? optional($cp->createdByUser)->employee_id,
                            'diterima_oleh' => optional($cp->receivedByUser)->name,
                            'employee_id_diterima_oleh' => optional(optional($cp->receivedByUser)->employee)->employee_id ?? optional($cp->receivedByUser)->employee_id,
                            'start_loading_oleh' => optional($cp->startedByUser)->name,
                            'employee_id_start_loading_oleh' => optional(optional($cp->startedByUser)->employee)->employee_id ?? optional($cp->startedByUser)->employee_id,
                            'cancel_oleh' => optional($cp->canceledByUser)->name,
                            'employee_id_cancel_oleh' => optional(optional($cp->canceledByUser)->employee)->employee_id ?? optional($cp->canceledByUser)->employee_id,
                            'waktu_cancel' => $cp->canceled_at ? $cp->canceled_at->toIso8601String() : null,
                            'note' => $cp->note,
                            'dibuat' => $cp->created_at ? $cp->created_at->toIso8601String() : null,
                            'diperbarui' => $cp->updated_at ? $cp->updated_at->toIso8601String() : null,
                        ];

                        echo json_encode($row, JSON_UNESCAPED_UNICODE);
                        $counter++;
                    }

                    // Flush output buffer to send data to client progressively
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                });

            // Close the JSON array
            echo ']';

        }, 200, [
            'Content-Type' => 'application/json',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering for streaming
        ]);
    }

    /**
     * GET /api/report/summary
     *
     * Returns only the summary statistics for the given filters.
     * Lightweight endpoint for PowerBI dashboard cards.
     */
    public function summary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $aktivitas = $request->input('aktivitas');
        $jenisBarang = $request->input('jenis_barang');
        $status = $request->input('status');
        $search = $request->input('search');

        $summary = $this->calculateSummary($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);

        return response()->json([
            'success' => true,
            'data' => $summary,
            'filters_applied' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'aktivitas' => $aktivitas,
                'jenis_barang' => $jenisBarang,
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }
}
