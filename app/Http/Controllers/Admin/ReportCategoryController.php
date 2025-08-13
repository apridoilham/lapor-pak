<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportCategoryRequest;
use App\Http\Requests\Admin\UpdateReportCategoryRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Traits\FileUploadTrait; // Tambahkan ini
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportCategoryController extends Controller
{
    use FileUploadTrait; // Gunakan Trait di sini

    private ReportCategoryRepositoryInterface $reportCategoryRepository;

    public function __construct(ReportCategoryRepositoryInterface $reportCategoryRepository)
    {
        $this->reportCategoryRepository = $reportCategoryRepository;
    }

    public function index()
    {
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        return view('pages.admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.admin.category.create');
    }

    public function store(StoreReportCategoryRequest $request)
    {
        $data = $request->validated();

        if ($path = $this->handleFileUpload($request, 'image', 'assets/category/image')) {
            $data['image'] = $path;
        }

        $this->reportCategoryRepository->createReportCategory($data);
        Swal::success('Berhasil', 'Kategori Laporan baru berhasil ditambahkan.');
        return redirect()->route('admin.report-category.index');
    }

    public function show(string $id)
    {
        $category = $this->reportCategoryRepository->getReportCategoryById($id);
        return view('pages.admin.category.show', compact('category'));
    }

    public function edit(string $id)
    {
        $category = $this->reportCategoryRepository->getReportCategoryById($id);
        return view('pages.admin.category.edit', compact('category'));
    }

    public function update(UpdateReportCategoryRequest $request, string $id)
    {
        $data = $request->validated();
        $oldImage = $this->reportCategoryRepository->getReportCategoryById($id)->image;

        if ($path = $this->handleFileUpload($request, 'image', 'assets/category/image', $oldImage)) {
            $data['image'] = $path;
        }

        $this->reportCategoryRepository->updateReportCategory($data, $id);
        Swal::success('Berhasil', 'Kategori Laporan berhasil diperbarui.');
        return redirect()->route('admin.report-category.index');
    }

    public function destroy(string $id)
    {
        $category = $this->reportCategoryRepository->getReportCategoryById($id);

        if ($category->image) {
            Storage::disk('public')->delete($category->image); // Gunakan Storage::disk('public')
        }

        $this->reportCategoryRepository->deleteReportCategory($id);
        Swal::success('Berhasil', 'Kategori Laporan berhasil dihapus.');
        return redirect()->route('admin.report-category.index');
    }

    // HAPUS metode private handleFileUpload dari sini karena sudah ada di Trait.
}