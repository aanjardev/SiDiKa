<?php

namespace App\Http\Requests;

use App\Services\DinoyoWayContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DinoyoWayUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=5000,max_height=5000'],
            'reset_hero_image' => ['nullable', 'boolean'],
        ];
        $keys = implode(',', array_keys(DinoyoWayContent::IMAGE_FIELDS));
        $rules['page_images'] = ['sometimes', 'array:'.$keys];
        $rules['reset_page_images'] = ['sometimes', 'array:'.$keys];
        foreach (DinoyoWayContent::IMAGE_FIELDS as $key => $path) {
            $rules['page_images.'.$key] = $rules['hero_image'];
            $rules['reset_page_images.'.$key] = ['nullable', 'boolean'];
        }
        $content = app(DinoyoWayContent::class)->editable(config('dinoyo-way'));
        $this->addRules($rules, 'content', $content);

        return $rules;
    }

    private function addRules(array &$rules, string $path, mixed $value): void
    {
        if (is_array($value)) {
            $rules[$path] = ['required', 'array:'.implode(',', array_keys($value))];
            foreach ($value as $key => $child) {
                $this->addRules($rules, $path.'.'.$key, $child);
            }
        } else {
            $rules[$path] = is_int($value)
                ? ['required', 'integer', 'between:0,100']
                : ['required', 'string', 'max:'.DinoyoWayContent::maxLength($path)];
            if (str_ends_with($path, '_url')) {
                $rules[$path][] = function ($attribute, $url, $fail) {
                    if (!is_string($url)) return;
                    $anchor = in_array($url, ['#filosofi', '#nilai', '#cara-kerja', '#bertumbuh', '#janji'], true);
                    $relative = str_starts_with($url, '/') && !str_starts_with($url, '//');
                    $web = filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true);
                    if (preg_match('/[\x00-\x20\x7f\\\\]/', $url) || (!$anchor && !$relative && !$web)) {
                        $fail('Gunakan tautan http/https, path lokal seperti /contact, atau anchor bagian halaman yang tersedia.');
                    }
                };
            }

        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $sum = collect($this->input('content.evaluation.items'))->sum('percentage');
            if ($sum != 100) {
                $validator->errors()->add('content.evaluation.items', 'Total bobot penilaian harus 100%.');
            }
            foreach (DinoyoWayContent::IMAGE_FIELDS as $key => $path) {
                if ($this->hasFile('page_images.'.$key) && $this->boolean('reset_page_images.'.$key)) {
                    $validator->errors()->add('page_images.'.$key, 'Pilih foto baru atau kembalikan foto awal, bukan keduanya.');
                }
            }
            if ($this->hasFile('hero_image') && $this->boolean('reset_hero_image')) {
                $validator->errors()->add('hero_image', 'Pilih foto baru atau kembalikan foto awal, bukan keduanya.');
            }
        });
    }

    public function attributes(): array
    {
        $attributes = ['hero_image' => 'foto hero'];
        foreach (DinoyoWayContent::IMAGE_FIELDS as $key => $path) {
            $attributes['page_images.'.$key] = 'foto '.str_replace('_', ' ', $key);
        }
        foreach (Arr::dot(app(DinoyoWayContent::class)->editable(config('dinoyo-way'))) as $path => $value) {
            $parts = explode('.', $path);
            $attributes['content.'.$path] = implode(' / ', array_map(
                fn ($key) => DinoyoWayContent::SECTIONS[$key] ?? DinoyoWayContent::LABELS[$key] ?? (is_numeric($key) ? (int) $key + 1 : $key),
                $parts
            ));
        }

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'array' => 'Susunan :attribute tidak valid. Muat ulang halaman dan coba lagi.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'between' => ':attribute harus antara :min dan :max.',
            'max.string' => ':attribute maksimal :max karakter.',
            'page_images.*.image' => 'Pilih file gambar yang valid.',
            'page_images.*.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'page_images.*.max' => 'Ukuran foto maksimal 4 MB.',
            'page_images.*.dimensions' => 'Resolusi foto maksimal 5000 x 5000 piksel.',
            'hero_image.image' => 'Pilih file gambar yang valid.',
            'hero_image.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'hero_image.max' => 'Ukuran foto maksimal 4 MB.',
            'hero_image.dimensions' => 'Resolusi foto maksimal 5000 × 5000 piksel.',
        ];
    }
}
