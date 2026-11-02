<?php

namespace App\Http\Requests;

use App\Models\Office;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('offices', 'code')->ignore($this->route('office')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('offices', 'slug')->ignore($this->route('office')),
            ],
            'address' => ['nullable', 'string', 'max:2000'],
            'attendance_method' => [
                'required',
                Rule::in([Office::ATTENDANCE_MACHINE, Office::ATTENDANCE_WEB_FACE]),
            ],
            'attendance_latitude' => ['nullable', 'required_if:attendance_method,'.Office::ATTENDANCE_WEB_FACE, 'numeric', 'between:-90,90'],
            'attendance_longitude' => ['nullable', 'required_if:attendance_method,'.Office::ATTENDANCE_WEB_FACE, 'numeric', 'between:-180,180'],
            'attendance_radius_meters' => ['required_if:attendance_method,'.Office::ATTENDANCE_WEB_FACE, 'nullable', 'integer', 'min:10', 'max:1000'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode kantor wajib diisi.',
            'code.regex' => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung.',
            'code.unique' => 'Kode kantor sudah digunakan.',
            'name.required' => 'Nama kantor wajib diisi.',
            'slug.required' => 'Slug kantor wajib diisi.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug kantor sudah digunakan.',
            'attendance_method.required' => 'Metode presensi wajib dipilih.',
            'attendance_method.in' => 'Metode presensi tidak valid.',
            'attendance_latitude.required_if' => 'Latitude wajib diisi untuk presensi Face ID.',
            'attendance_latitude.between' => 'Latitude harus berada antara -90 sampai 90.',
            'attendance_longitude.required_if' => 'Longitude wajib diisi untuk presensi Face ID.',
            'attendance_longitude.between' => 'Longitude harus berada antara -180 sampai 180.',
            'attendance_radius_meters.required_if' => 'Radius presensi wajib diisi untuk presensi Face ID.',
            'attendance_radius_meters.min' => 'Radius presensi minimal 10 meter.',
            'attendance_radius_meters.max' => 'Radius presensi maksimal 1.000 meter.',
            'sort_order.required' => 'Urutan kantor wajib diisi.',
        ];
    }
}
