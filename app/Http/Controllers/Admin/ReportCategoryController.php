<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportCategoryRequest;
use App\Http\Requests\UpdateReportCategoryRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Traits\FileUploadTrait; // <-- DITAMBAHKAN
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportCategoryController extends Controller
{
    use FileUploadTrait; // <-- DITAMBAHKAN

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
        
        // PERUBAHAN DI SINI
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-category/image')) {
            $data['image'] = $path;
        }

        $this->reportCategoryRepository->createReportCategory($data);

        Swal::success('Success', 'Data kategori berhasil ditambahkan!')->timerProgressBar();

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

        // PERUBAHAN DI SINI
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-category/image')) {
            $data['image'] = $path;
        }

        $this->reportCategoryRepository->updateReportCategory($data, $id);

        Swal::success('Success', 'Data kategori berhasil diubah!')->timerProgressBar();

        return redirect()->route('admin.report-category.index');
    }

    public function destroy(string $id)
    {
        $this->reportCategoryRepository->deleteReportCategory($id);

        Swal::success('Success', 'Data kategori berhasil dihapus!')->timerProgressBar();

        return redirect()->route('admin.report-category.index');
    }
}