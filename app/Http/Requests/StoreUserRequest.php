<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.users') ?? false;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', Rule::unique('surat_users', 'username')],
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'director_id' => ['nullable', 'exists:directors,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'nik' => ['nullable', 'string', 'max:20'],
        ];
    }
}
