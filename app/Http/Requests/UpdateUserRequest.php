<?php

namespace App\Http\Requests;

use App\Models\SuratUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.users') ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof SuratUser ? $user->id : $user;

        return [
            'username' => ['required', 'string', 'max:50', Rule::unique('surat_users', 'username')->ignore($userId)],
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'director_id' => ['nullable', 'exists:directors,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'nik' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
