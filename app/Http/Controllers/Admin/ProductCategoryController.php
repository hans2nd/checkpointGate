<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(10);
        
        return view('product-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return view('product-categories.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Check if it's being used in checkpoints
        if ($productCategory->checkpoints()->exists()) {
            return redirect()->back()
                ->with('error', 'Kategori produk tidak dapat dihapus karena sedang digunakan di data checkpoint.');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil dihapus.');
    }

    /**
     * Download the import template.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kategori Produk');

        // Headers
        $sheet->setCellValue('A1', 'ID (Kosongkan)');
        $sheet->setCellValue('B1', 'Name (Wajib)');
        $sheet->setCellValue('C1', 'Description (Opsional)');
        
        // Example Row
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', 'Sayuran');
        $sheet->setCellValue('C2', 'Kategori untuk produk sayuran segar');

        // Make headers bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Kategori_Produk.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        $data = ProductCategory::orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kategori Produk');

        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        
        // Make headers bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Data
        $row = 2;
        foreach ($data as $cat) {
            $sheet->setCellValue('A' . $row, $cat->id);
            $sheet->setCellValue('B' . $row, $cat->name);
            $sheet->setCellValue('C' . $row, $cat->description);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Master_Kategori_Produk_' . date('Ymd_His') . '.xlsx';
        $temp = storage_path('app/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import the resource from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $skippedCount = 0;
            $importedCount = 0;

            // Skip header (index 0)
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $name = trim((string)($row[1] ?? ''));
                $description = trim((string)($row[2] ?? ''));

                if (empty($name)) {
                    continue; // Skip empty rows
                }

                $existing = ProductCategory::where('name', $name)->first();
                if ($existing) {
                    $skippedCount++;
                } else {
                    ProductCategory::create([
                        'name' => $name,
                        'description' => $description,
                    ]);
                    $importedCount++;
                }
            }

            $message = "Import selesai! {$importedCount} data berhasil diimport.";
            if ($skippedCount > 0) {
                $message .= " Terdapat {$skippedCount} data yang di-skip karena nama duplikat.";
            }

            return redirect()->route('product-categories.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('product-categories.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
