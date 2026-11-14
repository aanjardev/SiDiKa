@foreach($fields as $key => $value)
    @php
        $fieldPath = $prefix.'.'.$key;
        $fieldId = 'way-'.str_replace('.', '-', $fieldPath);
        $parts = explode('.', $fieldPath);
        $inputName = array_shift($parts).implode('', array_map(fn($part) => '['.$part.']', $parts));
        $label = is_numeric($key) ? 'Item '.((int) $key + 1) : \Illuminate\Support\Str::headline($key);
        $inputValue = old($fieldPath, $value);
        $multiline = is_array($value) || in_array($key, ['description', 'statement', 'lead', 'meaning', 'application', 'definition', 'vision', 'note', 'translation', 'jihad_description', 'paragraphs']);
    @endphp

    @if(is_array($value))
        <div class="way-editor-item">
            <p class="way-editor-item-title">{{ $label }}</p>
            @include('admin.partials.dinoyo_way_fields', ['fields' => $value, 'prefix' => $fieldPath])
        </div>
    @else
        <div class="mb-3">
            <label for="{{ $fieldId }}" class="form-label">{{ $label }} <span class="text-danger" aria-hidden="true">*</span></label>
            @if($multiline)
                <textarea name="{{ $inputName }}" id="{{ $fieldId }}" rows="3" maxlength="{{ \App\Services\DinoyoWayContent::maxLength($fieldPath) }}" class="form-control @error($fieldPath) is-invalid @enderror" required>{{ $inputValue }}</textarea>
            @else
                <input type="text" name="{{ $inputName }}" id="{{ $fieldId }}" value="{{ $inputValue }}" maxlength="{{ \App\Services\DinoyoWayContent::maxLength($fieldPath) }}" class="form-control @error($fieldPath) is-invalid @enderror" required>
            @endif
            @error($fieldPath) <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endif
@endforeach
