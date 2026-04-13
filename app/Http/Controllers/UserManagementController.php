<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->input('role')) {
            $query->where('role_id', $roleId);
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = in_array($request->input('per_page'), [10, 15, 25, 50, 100]) ? (int) $request->input('per_page') : 15;

        $users = $query->orderBy('created_at', 'desc')
                       ->paginate($perPage)
                       ->withQueryString();

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')
                         ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
                         ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                             ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        // Prevent deleting yourself
        $ids = array_filter($request->ids, fn($id) => (int)$id !== auth()->id());

        User::whereIn('id', $ids)->delete();

        return redirect()->route('users.index')
                         ->with('success', count($ids) . ' user berhasil dihapus.');
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                             ->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('users.index')
                         ->with('success', "User {$user->name} berhasil {$status}.");
    }

    /**
     * Export users to Excel
     */
    public function export(Request $request)
    {
        $query = User::with('role');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->input('role')) {
            $query->where('role_id', $roleId);
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');

        // Header
        $headers = ['No', 'Nama', 'Email', 'Role', 'Status', 'Dibuat'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $row = 2;
        foreach ($data as $index => $user) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->role ? $user->role->display_name : '-');
            $sheet->setCellValue('E' . $row, $user->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('F' . $row, $user->created_at->format('d/m/Y H:i'));
            $row++;
        }

        // Data borders
        if ($row > 2) {
            $sheet->getStyle('A2:F' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'data_users_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import users from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        $skipped = 0;
        $roles = Role::pluck('id', 'name')->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $name = trim($row[0] ?? '');
            $email = strtolower(trim($row[1] ?? ''));
            $password = trim($row[2] ?? '');
            $roleName = strtolower(trim($row[3] ?? ''));

            if (empty($name) || empty($email) || empty($password)) {
                $skipped++;
                continue;
            }

            // Check duplicate email
            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            // Resolve role
            $roleId = $roles[$roleName] ?? $roles['operator'] ?? null;

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $roleId,
                'is_active' => true,
            ]);
            $imported++;
        }

        $message = "{$imported} user berhasil diimport.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati (duplikat/tidak valid).";
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template User');

        $headers = ['Nama', 'Email', 'Password (min 8 karakter)', 'Role (admin/operator/viewer)'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Sample data
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', 'john@example.com');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'operator');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_user.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
