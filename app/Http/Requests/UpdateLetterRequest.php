<?php

namespace App\Http\Requests;

use App\Models\Letter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $letter = $this->route('letter');

        if (! $user || ! $letter) {
            return false;
        }

        return $user->can('letters.update') && ($user->can('letters.view-all') || $letter->created_by === $user->id);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('location')) {
            $this->merge([
                'location' => Str::upper(trim($this->input('location'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'letter_type' => ['nullable', 'in:I,II,III,IV,V'],
            'agenda_series' => ['nullable', 'string', 'max:10'],
            'letter_no' => ['required', 'string', 'max:50'],
            'director_id' => ['nullable', 'exists:directors,id'],
            'subject' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'letter_date' => ['nullable', 'date'],
            'received_date' => ['nullable', 'date'],
            'pages' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:50'],
            'sender_unit_id' => ['nullable', 'exists:sender_units,id'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'director_recipients' => ['nullable', 'array'],
            'director_recipients.*' => ['exists:directors,id'],
            'department_recipients' => ['nullable', 'array'],
            'department_recipients.*' => ['exists:departments,id'],
            'follow_up' => ['nullable', 'boolean'],
        ];
    }
}
