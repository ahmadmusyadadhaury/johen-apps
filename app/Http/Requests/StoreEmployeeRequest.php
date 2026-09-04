<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:30', Rule::unique('employees', 'nik')->ignore($this->route('employee'))],
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'position' => 'nullable|string|max:255',
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'exists:positions,id',
            'main_position_id' => 'nullable|exists:positions,id',
            'division_ids' => 'nullable|array',
            'division_ids.*' => 'exists:divisions,id',
            'atasan' => 'nullable|string|max:255',
            'atasan2' => 'nullable|string|max:255',
            'tipe' => 'required|in:karyawan_aktif,calon_karyawan,mantan_karyawan',
            'jenis_kerja' => 'nullable|in:Operasional,Office',
            'jam_kerja' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_resign' => 'nullable|date|after_or_equal:tanggal_masuk',
            'catatan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tipe.required' => 'Tipe karyawan wajib dipilih.',
            'tipe.in' => 'Tipe karyawan tidak valid.',
            'division_ids.*.exists' => 'Divisi tidak ditemukan.',
            'tanggal_resign.after_or_equal' => 'Tanggal resign harus setelah atau sama dengan tanggal masuk.',
        ];
    }
}
