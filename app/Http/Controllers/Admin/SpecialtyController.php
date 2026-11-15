<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function index(): View
    {
        return view('admin.specialties.index', ['specialties' => Specialty::withCount('doctors')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120', Rule::unique('specialties', 'name')]]);
        $specialty = Specialty::create($data);
        AuditLog::record('specialty.create', $specialty);

        return back()->with('status', 'Specialty added.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        if ($specialty->doctors()->withTrashed()->exists()) {
            return back()->with('error', 'This specialty is used by doctors and cannot be deleted.');
        }

        AuditLog::record('specialty.delete', $specialty);
        $specialty->delete();

        return back()->with('status', 'Specialty deleted.');
    }
}
