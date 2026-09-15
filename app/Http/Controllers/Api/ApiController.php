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
use Illuminate\Support\Facades\Cache;
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
     * Supports: email+password (User Management) or employee_id only (Employee)
     */
    public function login(Request $request)
    {
        $loginMode = $request->input('login_mode', 'auto');

        // Auto-detect: if employee_id is provided and no email, use employee login
        if ($loginMode === 'employee' || ($request->filled('employee_id') && !$request->filled('email'))) {
            return $this->loginWithEmployeeId($request);
        }

        return $this->loginWithEmail($request);
    }

    /**
     * Traditional email + password login
     */
    protected function loginWithEmail(Request $request)
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

        if (Cache::get('app_maintenance', false) && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Aplikasi sedang maintenance. Tidak dapat login saat ini.',
            ], 503);
        }

        $appSource = $request->input('app_source', 'mobile1');
        if ($user->hasRole('security') && $appSource === 'mobile1') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Akun Security hanya dapat login di aplikasi Security (Mobile 2).',
            ], 403);
        }

        if ($user->hasRole('operator') && $appSource === 'mobile2') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Akun Operator hanya dapat login di aplikasi Operator (Mobile 1).',
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
                'auth_type' => 'user',
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Employee ID login (no password required)
     */
    protected function loginWithEmployeeId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $employee = \App\Models\Employee::where('employee_id', $request->employee_id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID tidak ditemukan.',
            ], 401);
        }

        if (!$employee->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun employee tidak aktif. Hubungi admin.',
            ], 403);
        }

        // Find or create shadow user for this employee
        $user = User::where('employee_id', $employee->id)->first();

        if (!$user) {
            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->employee_id . '@employee.local',
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                'role_id' => $employee->role_id,
                'is_active' => true,
                'employee_id' => $employee->id,
            ]);
        } else {
            // Sync role from employee
            $user->update([
                'name' => $employee->name,
                'role_id' => $employee->role_id,
                'is_active' => $employee->is_active,
            ]);
        }

        if (Cache::get('app_maintenance', false) && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Aplikasi sedang maintenance. Tidak dapat login saat ini.',
            ], 503);
        }

        $appSource = $request->input('app_source', 'mobile1');
        if ($user->hasRole('security') && $appSource === 'mobile1') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Akun Security hanya dapat login di aplikasi Security (Mobile 2).',
            ], 403);
        }

        if ($user->hasRole('operator') && $appSource === 'mobile2') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Akun Operator hanya dapat login di aplikasi Operator (Mobile 1).',
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
                    'name' => $employee->name,
                    'employee_id' => $employee->employee_id,
                    'role' => $employee->role->name ?? null,
                ],
                'auth_type' => 'employee',
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
            'inbound' => Checkpoint::whereDate('tanggal', $date)->where('aktivitas', 'INBOUND')->count(),
            'outbound' => Checkpoint::whereDate('tanggal', $date)->where('aktivitas', 'OUTBOUND')->count(),
            'parking' => Checkpoint::whereIn('status', ['START', 'PARKING', 'DOC IN', 'ASSIGN GATE', 'WAITING', 'READY'])->count(),
            'on_loading' => Checkpoint::where('status', 'ON LOADING')->count(),
            'finish' => Checkpoint::where('status', 'FINISH')->whereNotNull('waktu_penyerahan_dokumen')->count(),
            'frozen' => Checkpoint::whereDate('tanggal', $date)->where('jenis_barang', 'FROZEN')->count(),
            'dry' => Checkpoint::whereDate('tanggal', $date)->where('jenis_barang', 'DRY')->count(),
            'completed' => Checkpoint::where('status', 'COMPLETED')->whereDate('waktu_keluar', $date)->count(),
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
    public function recentAssignments(Request $request)
    {
        $since = $request->query('since');
        if (!$since) {
            return response()->json(['data' => []]);
        }

        $checkpoints = Checkpoint::whereNotNull('gate')
            ->where('updated_at', '>', Carbon::parse($since))
            ->get();

        $data = $checkpoints->map(function ($cp) {
            return [
                'id' => $cp->id,
                'no_polisi' => $cp->no_polisi,
                'gate' => $cp->gate,
                'jenis_barang' => $cp->jenis_barang,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function checkpoints(Request $request)
    {
        $query = Checkpoint::with(['createdByUser', 'receivedByUser', 'startedByUser', 'canceledByUser', 'productCategory']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_polisi', 'like', "%{$s}%")
                    ->orWhere('vendor', 'like', "%{$s}%")
                    ->orWhere('driver', 'like', "%{$s}%");
            });
        }

        if ($request->filled('aktivitas'))
            $query->where('aktivitas', $request->aktivitas);
        if ($request->filled('jenis_barang'))
            $query->where('jenis_barang', $request->jenis_barang);
        if ($request->filled('status')) {
            $statuses = array_map('trim', explode(',', $request->status));
            $query->whereIn('status', $statuses);
        }
        if ($request->filled('tanggal'))
            $query->whereDate('tanggal', $request->tanggal);

        if ($request->filled('waktu_penyerahan_dokumen')) {
            if (strtolower($request->waktu_penyerahan_dokumen) === 'is not null') {
                $query->whereNotNull('waktu_penyerahan_dokumen');
            } elseif (strtolower($request->waktu_penyerahan_dokumen) === 'is null') {
                $query->whereNull('waktu_penyerahan_dokumen');
            }
        }

        if ($request->filled('waktu_keluar_date')) {
            $query->whereDate('waktu_keluar', $request->waktu_keluar_date);
        }

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
        $cp = Checkpoint::with(['createdByUser', 'receivedByUser', 'startedByUser', 'canceledByUser', 'productCategory'])->findOrFail($id);

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
            'tipe' => 'nullable|in:INTERNAL,EKSTERNAL',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'jenis_barang' => 'nullable|in:FROZEN,DRY,CHILLED',
            'aktivitas' => 'nullable|in:INBOUND,OUTBOUND',
            'note' => 'nullable|string|max:500',
            'no_surat_jalan' => 'nullable|string|max:100',
            'purchase_order' => 'nullable|string|max:100',
            'type_of_load' => 'nullable|in:Full,Mix,Cross Dock',
            'product_category_id' => 'nullable|exists:product_categories,id|required_if:type_of_load,Full',
            'shipping_type' => 'nullable|string|max:100',
            'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'product_category_id.required_if' => 'Category product wajib diisi jika Type Of Load adalah Full.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Format Nomor Polisi (Contoh: B6677FAG -> B 6677 FAG)
        $noPolisiFormatted = strtoupper(str_replace(' ', '', $request->no_polisi));
        $noPolisiFormatted = preg_replace('/(?<=[A-Z])(?=[0-9])|(?<=[0-9])(?=[A-Z])/', ' ', $noPolisiFormatted);

        // Auto-insert Master Vehicle jika belum ada
        \App\Models\Vehicle::firstOrCreate(
            ['no_polisi' => $noPolisiFormatted],
            [
                'driver' => $request->driver,
                'vendor' => $request->vendor,
                'tipe' => $request->tipe ?? 'EKSTERNAL', // Default if not provided from mobile
                'jenis_kendaraan' => $request->jenis_kendaraan ?? '-',
            ]
        );

        $dataToSave = array_merge($request->only([
            'vendor',
            'driver',
            'tipe',
            'jenis_kendaraan',
            'jenis_barang',
            'aktivitas',
            'note',
            'no_surat_jalan',
            'purchase_order',
            'type_of_load',
            'product_category_id',
            'shipping_type',
        ]), [
            'no_polisi' => $noPolisiFormatted,
            'tanggal' => Carbon::today(),
            'created_by' => $request->user()->id,
        ]);

        if ($request->hasFile('foto_identitas')) {
            $file = $request->file('foto_identitas');
            $filename = $file->hashName();
            $path = 'checkpoints/' . $filename;

            // Bypass store() yang menggunakan getRealPath() di dalam FilesystemAdapter.
            // Di beberapa environment Windows (Laragon), getRealPath() pada file temp bisa me-return false
            // yang menyebabkan error ValueError: Path cannot be empty saat fopen(false, 'r').
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

            $dataToSave['foto_identitas'] = $path;
        }

        $cp = Checkpoint::create($dataToSave);

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
            'status' => 'DOC IN',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen diterima, Gate ' . $request->gate . ' ditetapkan.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/assign-gate
     * Assign Gate
     */
    public function assignGate(Request $request, $id)
    {
        $cp = Checkpoint::findOrFail($id);

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

        $validator = Validator::make($request->all(), [
            'gate' => 'required|integer|min:1|max:27',
            'jenis_barang' => 'nullable|in:FROZEN,DRY,CHILLED',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $gateNumber = (int) $request->gate;

        // Check if gate is already in use today
        $gateInUse = Checkpoint::whereDate('created_at', Carbon::today())
            ->where('gate', $gateNumber)
            ->whereKeyNot($cp->id)
            ->where('status', '!=', 'CANCEL')
            ->whereNull('waktu_end')
            ->exists();

        if ($gateInUse) {
            return response()->json([
                'success' => false,
                'message' => "Gate {$gateNumber} sedang digunakan untuk loading.",
            ], 422);
        }

        $updateData = [
            'gate' => $gateNumber,
            'status' => 'ASSIGN GATE'
        ];
        if ($request->has('jenis_barang')) {
            $updateData['jenis_barang'] = $request->jenis_barang;
        }

        $cp->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Gate ' . $gateNumber . ' ditetapkan.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/confirm-gate
     * Confirm Gate -> Changes status to READY
     */
    public function triggerConfirmGate($id)
    {
        $cp = Checkpoint::findOrFail($id);

        if ($cp->status === 'CANCEL') {
            return response()->json([
                'success' => false,
                'message' => "Checkpoint {$cp->no_polisi} sudah dibatalkan.",
            ], 422);
        }

        if (!$cp->waktu_penerimaan_dokumen) {
            return response()->json([
                'success' => false,
                'message' => "Penerimaan dokumen belum dilakukan.",
            ], 422);
        }

        if (!$cp->gate) {
            return response()->json([
                'success' => false,
                'message' => "Gate belum dipilih.",
            ], 422);
        }

        $cp->update([
            'status' => 'READY',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Gate {$cp->gate} dikonfirmasi. Kendaraan siap untuk loading.",
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

        // Must have checkpoint.trigger_start permission
        if (!$currentUser->hasPermission('checkpoint.trigger_start') && !$currentUser->isAdmin()) {
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

        // Must have checkpoint.trigger_end permission
        if (!$currentUser->hasPermission('checkpoint.trigger_end') && !$currentUser->isAdmin()) {
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
        // Exception: user with checkpoint.edit permission, or administrator
        if ($cp->started_by && $currentUser->id !== $cp->started_by) {
            $canOverride = $currentUser->hasPermission('checkpoint.edit') && $currentUser->hasPermission('checkpoint.trigger_end');

            if (!$currentUser->isAdmin() && !$canOverride) {
                $starterName = $cp->startedByUser?->name ?? 'Unknown';
                return response()->json([
                    'success' => false,
                    'message' => "Anda tidak dapat menyelesaikan loading ini. Loading dimulai oleh {$starterName}. Hanya user yang sama atau yang memiliki izin override (edit) yang dapat menyelesaikannya.",
                ], 403);
            }
        }

        $now = Carbon::now();
        $durasi = null;
        if ($cp->waktu_start) {
            $diff = $cp->waktu_start->diff($now);
            $hours = ($diff->days * 24) + $diff->h;
            $durasi = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
        }

        if ($cp->aktivitas === 'INBOUND' && strtolower($cp->type_of_load) === 'cross dock') {
            $waktuStart = $cp->waktu_start ?? $cp->waktu_penerimaan_dokumen ?? $now;
            $diff = $waktuStart->diff($now);
            $hours = ($diff->days * 24) + $diff->h;
            $durasiCd = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);

            $cp->update([
                'waktu_start' => $waktuStart,
                'waktu_end' => $now,
                'waktu_penyerahan_dokumen' => $now,
                'status' => 'COMPLETED',
                'durasi' => $durasiCd,
            ]);

            \App\Models\Checkpoint::create([
                'no_polisi' => $cp->no_polisi,
                'vendor' => $cp->vendor,
                'driver' => $cp->driver,
                'tipe' => $cp->tipe,
                'jenis_kendaraan' => $cp->jenis_kendaraan,
                'jenis_barang' => $cp->jenis_barang,
                'aktivitas' => 'OUTBOUND',
                'note' => $cp->note,
                'no_surat_jalan' => $cp->no_surat_jalan,
                'purchase_order' => $cp->purchase_order,
                'type_of_load' => 'Cross Dock',
                'product_category_id' => $cp->product_category_id,
                'shipping_type' => $cp->shipping_type,
                'tanggal' => \Carbon\Carbon::today(),
                'status' => 'PARKING',
                'created_by' => $currentUser->id,
                'is_generated_cross_dock' => true,
            ]);
        } else {
            $cp->update([
                'waktu_end' => $now,
                'status' => 'FINISH',
                'durasi' => $durasi,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Loading selesai. Durasi: ' . $durasi,
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/trigger-penyerahan
     */
    public function triggerPenyerahan(Request $request, $id)
    {
        $cp = Checkpoint::findOrFail($id);
        
        $isCrossDock = strtolower($cp->type_of_load) === 'cross dock';
        $isInbound = strtoupper($cp->aktivitas) === 'INBOUND';

        $request->validate([
            'receipt_number' => ($isCrossDock || !$isInbound) ? 'nullable|string|max:100' : 'required|string|max:100',
        ], [
            'receipt_number.required' => 'Nomor Receipt wajib diisi saat Serah Dokumen.',
        ]);

        if (strtolower($cp->type_of_load) !== 'cross dock' && $request->filled('receipt_number')) {
            $receipts = explode('/', $request->receipt_number);
            foreach ($receipts as $receipt) {
                $receipt = trim($receipt);
                if (empty($receipt)) continue;

                $exists = Checkpoint::where('id', '!=', $cp->id)
                    ->where(function ($query) use ($receipt) {
                        $query->where('receipt_number', $receipt)
                            ->orWhere('receipt_number', 'LIKE', $receipt . '/%')
                            ->orWhere('receipt_number', 'LIKE', '%/' . $receipt . '/%')
                            ->orWhere('receipt_number', 'LIKE', '%/' . $receipt);
                    })->first();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => "Nomor Receipt {$receipt} sudah pernah diinput pada checkpoint lain (No Polisi: {$exists->no_polisi})."
                    ], 422);
                }
            }
        }

        $cp->update([
            'waktu_penyerahan_dokumen' => Carbon::now(),
            'waktu_keluar' => Carbon::now(),
            'receipt_number' => $request->receipt_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen diserahkan.',
            'data' => $this->formatCheckpoint($cp->fresh()),
        ]);
    }

    /**
     * POST /api/checkpoints/{id}/trigger-completed
     */
    public function triggerCompleted($id)
    {
        $cp = Checkpoint::findOrFail($id);
        $cp->update([
            'waktu_keluar' => Carbon::now(),
            'status' => 'COMPLETED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kendaraan selesai (Completed).',
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
            ->whereNull('waktu_end')
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
            'type_of_load' => $cp->type_of_load,
            'product_category_id' => $cp->product_category_id,
            'product_category_name' => $cp->productCategory?->name,
            'shipping_type' => $cp->shipping_type,
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
            'waktu_keluar' => $cp->waktu_keluar?->toIso8601String(),
            'created_at' => $cp->created_at?->toIso8601String(),
            'foto_identitas_url' => $cp->foto_identitas ? asset('storage/' . $cp->foto_identitas) : null,
        ];
    }
}
