<?php

namespace App\Http\Traits;

use App\Models\Checkpoint;
use Carbon\Carbon;

/**
 * Shared report query logic used by both web ReportController and API ReportApiController.
 *
 * Provides:
 * - buildReportQuery()       — main query with date-range, filters, and search
 * - formatGateLabel()        — converts gate number to display label (F-1, D-3, etc.)
 * - calculateDurasiDokumen() — calculates document processing duration
 * - calculateWaktuTunggu()   — calculates waiting time from creation to document receipt
 * - calculateSummary()       — calculates summary statistics for the query
 */
trait ReportQueryTrait
{
    /**
     * Build the main report query with date-range, overnight logic, and filters.
     */
    protected function buildReportQuery(
        ?string $startDate,
        ?string $endDate,
        ?string $aktivitas = null,
        ?string $jenisBarang = null,
        ?string $status = null,
        ?string $search = null
    ) {
        $query = Checkpoint::with(['createdByUser', 'receivedByUser', 'startedByUser', 'canceledByUser']);

        if ($startDate && $endDate) {
            $parsedStart = Carbon::parse($startDate)->startOfDay();
            $parsedEnd = Carbon::parse($endDate)->endOfDay();
            $dayBeforeStart = Carbon::parse($startDate)->subDay();

            $query->where(function ($dateScope) use ($parsedStart, $parsedEnd, $dayBeforeStart) {
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
        }

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

    /**
     * Format gate number to display label.
     */
    protected function formatGateLabel($gate): string
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

    /**
     * Calculate document processing duration (penerimaan → penyerahan).
     */
    protected function calculateDurasiDokumen(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->waktu_penerimaan_dokumen || !$checkpoint->waktu_penyerahan_dokumen)
            return '';
        $diff = $checkpoint->waktu_penerimaan_dokumen->diff($checkpoint->waktu_penyerahan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;
        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }

    /**
     * Calculate waiting time (created_at → waktu_penerimaan_dokumen).
     */
    protected function calculateWaktuTunggu(Checkpoint $checkpoint): string
    {
        if (!$checkpoint->created_at || !$checkpoint->waktu_penerimaan_dokumen)
            return '';
        $diff = $checkpoint->created_at->diff($checkpoint->waktu_penerimaan_dokumen);
        $hours = ($diff->days * 24) + $diff->h;
        return sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
    }

    /**
     * Calculate summary statistics for a given query.
     */
    protected function calculateSummary(
        ?string $startDate,
        ?string $endDate,
        ?string $aktivitas = null,
        ?string $jenisBarang = null,
        ?string $status = null,
        ?string $search = null
    ): array {
        $summaryQuery = $this->buildReportQuery($startDate, $endDate, $aktivitas, $jenisBarang, $status, $search);
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

        return [
            'total_kendaraan' => $totalKendaraan,
            'total_finish' => $totalFinish,
            'total_on_loading' => $totalOnLoading,
            'total_cancel' => $totalCancel,
            'avg_durasi_loading' => $avgDurasi,
        ];
    }
}
