<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration_minutes' => ['required', 'integer', 'between:5,480'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
        ];
    }

    public function index(): View
    {
        return view('admin.services.index', ['services' => Service::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $service = Service::create($data + ['is_active' => true]);
        AuditLog::record('service.create', $service);

        return back()->with('status', 'Service added.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $service->update($data + ['is_active' => $request->boolean('is_active')]);
        AuditLog::record('service.update', $service);

        return back()->with('status', 'Service saved.');
    }
}
