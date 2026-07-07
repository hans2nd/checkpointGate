<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     *
     * Automatically reset the sync flag when any data field changes,
     * so the SyncCheckpointsCommand will detect and push updates to VPS.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($checkpoint) {
            // If any field other than 'sync' has changed, reset sync flag
            $changedFields = array_keys($checkpoint->getDirty());
            $realChanges = array_diff($changedFields, ['sync']);

            if (!empty($realChanges)) {
                $checkpoint->sync = 0;
            }
        });
    }

    protected $fillable = [
        'tanggal',
        'no_polisi',
        'vendor',
        'driver',
        'tipe',
        'jenis_kendaraan',
        'waktu_penerimaan_dokumen',
        'waktu_penyerahan_dokumen',
        'waktu_keluar',
        'jenis_barang',
        'aktivitas',
        'gate',
        'status',
        'waktu_start',
        'waktu_end',
        'durasi',
        'created_by',
        'started_by',
        'note',
        'no_surat_jalan',
        'purchase_order',
        'cancel_note',
        'canceled_at',
        'canceled_by',
        'cancel_status',
        'cancel_reason',
        'cancel_requested_by',
        'cancel_approved_by',
        'created_at',
        'foto_identitas',
        'received_by',
        'receipt_number',
        'sync',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_penerimaan_dokumen' => 'datetime',
        'waktu_penyerahan_dokumen' => 'datetime',
        'waktu_keluar' => 'datetime',
        'waktu_start' => 'datetime',
        'waktu_end' => 'datetime',
        'canceled_at' => 'datetime',
        'gate' => 'string',
    ];

    /**
     * User who created this checkpoint record.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who started the loading process.
     */
    public function startedByUser()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * User who canceled this checkpoint record.
     */
    public function canceledByUser()
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    /**
     * User who requested the cancel.
     */
    public function cancelRequester()
    {
        return $this->belongsTo(User::class, 'cancel_requested_by');
    }

    /**
     * User who approved the cancel.
     */
    public function cancelApprover()
    {
        return $this->belongsTo(User::class, 'cancel_approved_by');
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the formatted gate label.
     */
    public function getFormattedGateAttribute()
    {
        if (!$this->gate) {
            return '-';
        }

        if (strtoupper($this->jenis_barang) === 'DRY') {
            $gateNum = $this->gate > 16 ? $this->gate - 16 : $this->gate;
            return 'D-' . $gateNum;
        } elseif (strtoupper($this->jenis_barang) === 'FROZEN') {
            return 'F-' . $this->gate;
        }

        if ($this->gate >= 1 && $this->gate <= 16) {
            return 'F-' . $this->gate;
        } elseif ($this->gate >= 17 && $this->gate <= 27) {
            return 'D-' . ($this->gate - 16);
        }

        return 'Gate-' . $this->gate;
    }
}
