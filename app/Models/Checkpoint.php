<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'no_polisi',
        'vendor',
        'driver',
        'tipe',
        'jenis_kendaraan',
        'waktu_penerimaan_dokumen',
        'waktu_penyerahan_dokumen',
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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_penerimaan_dokumen' => 'datetime',
        'waktu_penyerahan_dokumen' => 'datetime',
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
}
