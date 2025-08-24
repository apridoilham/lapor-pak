<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportCategoryRequest;
use App\Http\Requests\Admin\UpdateReportCategoryRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Models\ReportCategory;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportCategoryController extends Controller
{
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
        $this->reportCategoryRepository->createReportCategory($request->validated());
        Swal::success('Berhasil', 'Kategori Laporan baru berhasil ditambahkan.');
        return redirect()->route('admin.report-category.index');
    }

    public function show(ReportCategory $report_category)
    {
        $report_category->load(['reports', 'reports.resident.user', 'reports.latestStatus']);
        return view('pages.admin.category.show', compact('report_category'));
    }

    public function edit(ReportCategory $report_category)
    {
        return view('pages.admin.category.edit', compact('report_category'));
    }

    public function update(UpdateReportCategoryRequest $request, ReportCategory $report_category)
    {
        $this->reportCategoryRepository->updateReportCategory($request->validated(), $report_category->id);
        Swal::success('Berhasil', 'Kategori Laporan berhasil diperbarui.');
        // PERUBAHAN DI SINI: Arahkan ke halaman detail
        return redirect()->route('admin.report-category.show', $report_category);
    }

    public function destroy(ReportCategory $report_category)
    {
        if ($report_category->reports()->exists()) {
            Swal::error('Gagal', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh laporan.');
            return back();
        }

        $this->reportCategoryRepository->deleteReportCategory($report_category->id);
        Swal::success('Berhasil', 'Kategori Laporan berhasil dihapus.');
        return redirect()->route('admin.report-category.index');
    }
}