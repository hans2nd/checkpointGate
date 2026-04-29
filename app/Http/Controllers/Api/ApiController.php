<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\Gate;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Carbon::setLocale('id');

class ApiController extends Controller
{
    // ===================================================================
    // AUTH ENDPOINTS
    // ===================================================================

    /**
     * POST /api/login
     * Login and return API token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi admin.',
            ], 403);
        }

        $deviceName = $request->device_name ?? 'flutter-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name ?? null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * POST /api/logout
     * Revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/me
     * Get current authenticated user info
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role.permissions');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ? [
                    'name' => $user->role->name,
                    'display_name' => $user->role->display_name,
                    'permissions' => $user->role->permissions->pluck('name'),
                ] : null,
            ],
        ]);
    }

    // ===================================================================
    // DASHBOARD ENDPOINTS
    // ===================================================================

    /**
     * GET /api/dashboard
     * Dashboard summary stats
     */
    public function dashboard(Request $request)
    {
        $date = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::today();

        $stats = [
            'total_today' => Checkpoint::whereDate('tanggal', $date)->count(),
            'total_all' => Checkpoint::count(),
            'inbound' => Checkpoint::whereDate('tanggal', $date)->where('aktivitas', 'INBOUND')->count(),
            'outbound' => Checkpoint::whereDate('tanggal', $date)->where('aktivitas', 'OUTBOUND')->count(),
            'on_loading' => Checkpoint::whereDate('tanggal', $date)->where('status', 'ON LOADING')->count(),
            'finish' => Checkpoint::whereDate('tanggal', $date)->where('status', 'FINISH')->count(),
            'frozen' => Checkpoint::whereDate('tanggal', $date)->where('jenis_barang', 'FROZEN')->count(),
            'dry' => Checkpoint::whereDate('tanggal', $date)->where('jenis_barang', 'DRY')->count(),
        ];

        $recent = Checkpoint::whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($cp) => $this->formatCheckpoint($cp));

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'stats' => $stats,
                'recent' => $recent,
            ],
        ]);
    }

    /**
     * GET /api/chart-data
     * Chart data for dashboard charts
     */
    public function chartData(Request $request)
    {
        $days = $request->input('days', 7);

        $dailyData = Checkpoint::select(
                DB::raw("DATE(tanggal) as date"),
                DB::raw("SUM(CASE WHEN aktivitas = 'INBOUND' THEN 1 ELSE 0 END) as inbound"),
                DB::raw("SUM(CASE WHEN aktivitas = 'OUTBOUND' THEN 1 ELSE 0 END) as outbound"),
                DB::raw("COUNT(*) as total")
            )
            ->where('tanggal', '>=', Carbon::today()->subDays($days))
            ->groupBy(DB::raw("DATE(tanggal)"))
            ->orderBy('date', 'asc')
            ->get();

        $goodsData = Checkpoint::select('jenis_barang', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_barang')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'daily' => $dailyData,
                'goods' => $goodsData,
            ],
        ]);
    }

    // ===================================================================
    // LIVE MONITORING ENDPOINTS
    // ===================================================================

    /**
     * GET /api/live-monitoring
     * Gate grid data for live monitoring
     */
    public function liveMonitoring(Request $request)
    {
        $date = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::today();

        $activeCheckpoints = Checkpoint::whereDate('tanggal', $date)
            ->whereNotNull('gate')
            ->where('status', '!=', 'CANCEL')
            ->orderBy('gate')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('gate');

        $gates = [];
        for ($i = 1; $i <= 27; $i++) {
            $cp = isset($activeCheckpoints[$i]) ? $activeCheckpoints[$i]->first() : null;
            $gates[] = [
                'nomor' => $i,
                'jenis_barang' => $i <= 16 ? 'FROZEN' : 'DRY',
                'display_name' => $i <= 16 ? 'F-' . $i : 'D-' . ($i - 16),
                'checkpoint' => $cp ? $this->formatCheckpoint($cp) : null,
            ];
        }

        // Activity summary
        $activitySummary = [];
        foreach (['INBOUND', 'OUTBOUND'] as $aktivitas) {
            foreach (['FROZEN', 'DRY'] as $jenis) {
                $activitySummary[] = [
                    'label' => ($aktivitas === 'INBOUND' ? 'LOADING' : 'UNLOADING') . ' ' . $jenis,
                    'aktivitas' => $aktivitas,
                    'jenis_barang' => $jenis,
                    'on_process' => Checkpoint::whereDate('tanggal', $date)
                        ->where('aktivitas', $aktivitas)->where('jenis_barang', $jenis)
                        ->where('status', 'ON LOADING')->count(),
                    'finish' => Checkpoint::whereDate('tanggal', $date)
                        ->where('aktivitas', $aktivitas)->where('jenis_barang', $jenis)
                        ->where('status', 'FINISH')->count(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'is_today' => $date->isToday(),
                'gates' => $gates,
                'activity_summary' => $activitySummary,
            ],
        ]);
    }

    // ===================================================================
    // CHECKPOINT CRUD ENDPOINTS
    // ===================================================================

    /**
     * GET /api/checkpoints
     * List checkpoints with filters and pagination
     */
    public function checkpoints(Request $request)
    {
        $query = Checkpoint::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_polisi', 'like', "%{$s}%")
                  ->orWhere('vendor', 'like', "%{$s}%")
                  ->orWhere('driver', 'like', "%{$s}%");
            });
        }

        if ($request->filled('aktivitas')) $query->where('aktivitas', $request->aktivitas);
        if ($request->filled('jenis_barang')) $query->where('jenis_barang', $request->jenis_barang);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tanggal')) $query->whereDate('tanggal', $request->tanggal);

        $perPage = min($request->input('per_page', 15), 100);
        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data->items() ? collect($data->items())->map(fn($cp) => $this->formatCheckpoint($cp)) : [],
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    /**
     * GET /api/checkpoints/{id}
     * Get single checkpoint detail
     */
    public function checkpointShow($id)
    {
        $cp = Checkpoint::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatCheckpoint($cp),
        ]);
    }

    /**
     * POST /api/checkpoints
     * Create new checkpoint
     */
    public function checkpointStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_polisi' => 'required|string|max:20',
            'vendor' => 'required|string|max:100',
            'driver' => 'required|string|max:100',
            'tipe' => 'required|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'required|string|max:50',
            'jenis_barang' => 'required|in:FROZEN,DRY,CHILLED',
            'aktivitas' => 'required|in:INBOUND,OUTBOUND',
            'note' => 'nullable|string|max:500',
            'no_surat_jalan' => 'nullable|string|max:100',
            'purchase_order' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cp = Checkpoint::create(array_merge($request->only([
            'no_polisi', 'vendor', 'driver', 'tipe',
            'jenis_kendaraan', 'jenis_barang', 'aktivitas', 'note',
            'no_surat_jalan', 'purchase_order',
        ]), [
            'tanggal' => Carbon::today(),
            'status' => 'START',
            'created_by' => $request->user()->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Checkpoint berhasil ditambahkan.',
            'data' => $this->formatCheckpoint($cp),
        ], 201);
    }

    // ===================================================================
    // TRIGGER ENDPOINTS
    // ===================================================================

    /**
     * POST /api/checkpoints/{id}/trigger-penerimaan
     * Accept document & assign gate
     */
    public function triggerPenerimaan(Request $request, $id)
    {
        $cp = Checkpoint::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'gate' => 'required|integer|min:1|max:27',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cp->update([
            'waktu_penerimaan_dokumen' => Carbon::now(),
            'gate' => $request->gate,
            'status' => 'START',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen diterima, Gate ' . $request->gate . ' ditetapkan.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/trigger-start
     */
    public function triggerStart(Request $request, $id)
    {
        $cp = Checkpoint::findOrFail($id);
        $currentUser = $request->user();

        // Must have checkpoint.trigger permission
        if (!$currentUser->hasPermission('checkpoint.trigger') && !$currentUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan start loading.',
            ], 403);
        }

        if ($cp->status === 'CANCEL') {
            return response()->json([
                'success' => false,
                'message' => "Checkpoint {$cp->no_polisi} sudah dibatalkan.",
            ], 422);
        }

        if ($cp->status === 'FINISH') {
            return response()->json([
                'success' => false,
                'message' => "Checkpoint {$cp->no_polisi} sudah selesai.",
            ], 422);
        }

        $cp->update([
            'waktu_start' => Carbon::now(),
            'status' => 'ON LOADING',
            'started_by' => $currentUser->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Loading dimulai.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/trigger-end
     */
    public function triggerEnd(Request $request, $id)
    {
        $cp = Checkpoint::findOrFail($id);
        $currentUser = $request->user();

        // Must have checkpoint.trigger permission
        if (!$currentUser->hasPermission('checkpoint.trigger') && !$currentUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan end loading.',
            ], 403);
        }

        if ($cp->status === 'CANCEL') {
            return response()->json([
                'success' => false,
                'message' => "Checkpoint {$cp->no_polisi} sudah dibatalkan.",
            ], 422);
        }

        // Validate: user who ends must be the same who started
        // Exception: supervisor_admin with checkpoint.trigger, or administrator
        if ($cp->started_by && $currentUser->id !== $cp->started_by) {
            $isSupervisorWithTrigger = $currentUser->hasRole('supervisor_admin') && $currentUser->hasPermission('checkpoint.trigger');

            if (!$currentUser->isAdmin() && !$isSupervisorWithTrigger) {
                $starterName = $cp->startedByUser?->name ?? 'Unknown';
                return response()->json([
                    'success' => false,
                    'message' => "Anda tidak dapat menyelesaikan loading ini. Loading dimulai oleh {$starterName}. Hanya user yang sama atau Supervisor yang dapat menyelesaikan loading.",
                ], 403);
            }
        }

        $durasi = null;
        if ($cp->waktu_start) {
            $diff = $cp->waktu_start->diff(Carbon::now());
            $hours = ($diff->days * 24) + $diff->h;
            $durasi = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
        }

        $cp->update([
            'waktu_end' => Carbon::now(),
            'status' => 'FINISH',
            'durasi' => $durasi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Loading selesai. Durasi: ' . $durasi,
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/trigger-penyerahan
     */
    public function triggerPenyerahan($id)
    {
        $cp = Checkpoint::findOrFail($id);
        $cp->update([
            'waktu_penyerahan_dokumen' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen diserahkan.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    // ===================================================================
    // GATE & VEHICLE ENDPOINTS
    // ===================================================================

    /**
     * GET /api/gates/available
     * Get available gates
     */
    public function availableGates()
    {
        $occupiedGates = Checkpoint::whereDate('tanggal', Carbon::today())
            ->whereNotNull('gate')
            ->where('status', '!=', 'CANCEL')
            ->where(function ($q) {
                $q->whereNull('waktu_penyerahan_dokumen')
                  ->orWhere('status', '!=', 'FINISH');
            })
            ->pluck('gate')
            ->toArray();

        $gates = [];
        for ($i = 1; $i <= 27; $i++) {
            $gates[] = [
                'nomor' => $i,
                'jenis_barang' => $i <= 16 ? 'FROZEN' : 'DRY',
                'display_name' => $i <= 16 ? 'F-' . $i : 'D-' . ($i - 16),
                'available' => !in_array($i, $occupiedGates),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $gates,
        ]);
    }

    /**
     * GET /api/vehicles/search
     * Search vehicles for autocomplete
     */
    public function vehicleSearch(Request $request)
    {
        $term = $request->input('term', '');
        if (strlen($term) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $vehicles = Vehicle::where('no_polisi', 'like', "%{$term}%")
            ->limit(10)
            ->get(['id', 'no_polisi', 'driver', 'vendor', 'jenis_kendaraan', 'tipe']);

        return response()->json([
            'success' => true,
            'data' => $vehicles,
        ]);
    }

    // ===================================================================
    // HELPER
    // ===================================================================

    private function formatCheckpoint(Checkpoint $cp): array
    {
        $durasiDokumen = null;
        if ($cp->waktu_penerimaan_dokumen && $cp->waktu_penyerahan_dokumen) {
            $diff = $cp->waktu_penerimaan_dokumen->diff($cp->waktu_penyerahan_dokumen);
            $hours = ($diff->days * 24) + $diff->h;
            $durasiDokumen = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
        }

        return [
            'id' => $cp->id,
            'tanggal' => $cp->tanggal
    ? Carbon::parse($cp->tanggal)->translatedFormat('d M Y')
    : null,
            'no_polisi' => $cp->no_polisi,
            'vendor' => $cp->vendor,
            'driver' => $cp->driver,
            'tipe' => $cp->tipe,
            'jenis_kendaraan' => $cp->jenis_kendaraan,
            'jenis_barang' => $cp->jenis_barang,
            'aktivitas' => $cp->aktivitas,
            'gate' => $cp->gate,
            'status' => $cp->status,
            'durasi' => $cp->durasi,
            'durasi_dokumen' => $durasiDokumen,
            'note' => $cp->note,
            'no_surat_jalan' => $cp->no_surat_jalan,
            'purchase_order' => $cp->purchase_order,
            'cancel_note' => $cp->cancel_note,
            'canceled_at' => $cp->canceled_at?->toIso8601String(),
            'canceled_by' => $cp->canceled_by,
            'canceled_by_name' => $cp->canceledByUser?->name,
            'created_by' => $cp->created_by,
            'created_by_name' => $cp->createdByUser?->name,
            'started_by' => $cp->started_by,
            'started_by_name' => $cp->startedByUser?->name,
            'waktu_penerimaan_dokumen' => $cp->waktu_penerimaan_dokumen?->toIso8601String(),
            'waktu_start' => $cp->waktu_start?->toIso8601String(),
            'waktu_end' => $cp->waktu_end?->toIso8601String(),
            'waktu_penyerahan_dokumen' => $cp->waktu_penyerahan_dokumen?->toIso8601String(),
            'created_at' => $cp->created_at?->toIso8601String(),
        ];
    }
}
