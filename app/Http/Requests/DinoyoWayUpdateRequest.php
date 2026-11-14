<?php

namespace App\Http\Requests;

use App\Services\DinoyoWayContent;
use Illuminate\Foundation\Http\FormRequest;

class DinoyoWayUpdateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        $rules = ['content' => ['required', 'array']];
        $this->addRules($rules, 'content', app(DinoyoWayContent::class)->editable(config('dinoyo-way')));
        return $rules;
    }

    private function addRules(array &$rules, string $path, mixed $value): void
    {
        if (is_array($value)) {
            $rules[$path] = ['required', 'array:'.implode(',', array_keys($value))];
            foreach ($value as $key => $child) $this->addRules($rules, $path.'.'.$key, $child);
            return;
        }
        $rules[$path] = ['required', 'string', 'max:'.DinoyoWayContent::maxLength($path)];
    }

    public function attributes(): array { return []; }

    public function messages(): array
    {
        return ['required' => ':attribute wajib diisi.', 'array' => 'Susunan :attribute tidak valid.', 'string' => ':attribute harus berupa teks.', 'max.string' => ':attribute maksimal :max karakter.'];
    }
}
