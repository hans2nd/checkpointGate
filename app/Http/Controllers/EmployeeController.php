<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('role');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
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

        $employees = $query->orderBy('created_at', 'desc')
                           ->paginate($perPage)
                           ->withQueryString();

        $roles = Role::all();

        return view('employees.index', compact('employees', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|max:50|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'create_user' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $employee = Employee::create([
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'],
        ]);

        // Optionally also create a linked user
        if ($request->boolean('create_user')) {
            User::create([
                'name' => $employee->name,
                'email' => $employee->employee_id . '@employee.local',
                'password' => Hash::make(Str::random(32)),
                'role_id' => $employee->role_id,
                'is_active' => $employee->is_active,
                'employee_id' => $employee->id,
            ]);
        }

        return redirect()->route('employees.index')
                         ->with('success', 'Employee berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $roles = Role::all();
        $hasLinkedUser = User::where('employee_id', $employee->id)->exists();
        return view('employees.edit', compact('employee', 'roles', 'hasLinkedUser'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('employees')->ignore($employee->id)],
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
        ];

        $employee->update($data);

        // Sync linked user role if exists
        $linkedUser = User::where('employee_id', $employee->id)->first();
        if ($linkedUser) {
            $linkedUser->update([
                'name' => $employee->name,
                'role_id' => $employee->role_id,
                'is_active' => $employee->is_active,
            ]);
        }

        return redirect()->route('employees.index')
                         ->with('success', 'Employee berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        // Unlink any connected user before deleting
        User::where('employee_id', $employee->id)->update(['employee_id' => null]);

        $employee->delete();

        return redirect()->route('employees.index')
                         ->with('success', 'Employee berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        // Unlink users first
        User::whereIn('employee_id', $request->ids)->update(['employee_id' => null]);

        Employee::whereIn('id', $request->ids)->delete();

        return redirect()->route('employees.index')
                         ->with('success', count($request->ids) . ' employee berhasil dihapus.');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);

        // Sync linked user status
        $linkedUser = User::where('employee_id', $employee->id)->first();
        if ($linkedUser) {
            $linkedUser->update(['is_active' => $employee->is_active]);
        }

        $status = $employee->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('employees.index')
                         ->with('success', "Employee {$employee->name} berhasil {$status}.");
    }

    /**
     * Export employees to Excel
     */
    public function export(Request $request)
    {
        $query = Employee::with('role');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
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
        $sheet->setTitle('Data Employee');

        $headers = ['No', 'Employee ID', 'Nama', 'Role', 'Status', 'Dibuat'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 2;
        foreach ($data as $index => $emp) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $emp->employee_id);
            $sheet->setCellValue('C' . $row, $emp->name);
            $sheet->setCellValue('D' . $row, $emp->role ? $emp->role->display_name : '-');
            $sheet->setCellValue('E' . $row, $emp->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('F' . $row, $emp->created_at->format('d/m/Y H:i'));
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('A2:F' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'data_employees_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import employees from Excel
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
            if ($index === 0) continue;

            $employeeId = trim($row[0] ?? '');
            $name = trim($row[1] ?? '');
            $roleName = strtolower(trim($row[2] ?? ''));

            if (empty($employeeId) || empty($name)) {
                $skipped++;
                continue;
            }

            // Check duplicate
            if (Employee::where('employee_id', $employeeId)->exists()) {
                $skipped++;
                continue;
            }

            $roleId = $roles[$roleName] ?? $roles['operator'] ?? null;

            Employee::create([
                'employee_id' => $employeeId,
                'name' => $name,
                'role_id' => $roleId,
                'is_active' => true,
            ]);
            $imported++;
        }

        $message = "{$imported} employee berhasil diimport.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati (duplikat/tidak valid).";
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Employee');

        $headers = ['Employee ID', 'Nama', 'Role (admin/operator/viewer)'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->setCellValue('A2', 'EMP001');
        $sheet->setCellValue('B2', 'Nama Employee');
        $sheet->setCellValue('C2', 'operator');

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_employee.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
