@foreach($fields as $key => $value)
    @php
        $fieldPath = $prefix.'.'.$key;
        $fieldId = 'way-'.str_replace('.', '-', $fieldPath);
        $parts = explode('.', $fieldPath);
        $inputName = array_shift($parts).implode('', array_map(fn($part) => '['.$part.']', $parts));
        $parent = \Illuminate\Support\Str::afterLast($prefix, '.');
        $label = is_numeric($key)
            ? (($labels[$parent] ?? 'Elemen').' '.($key + 1))
            : ($labels[$key] ?? $key);
    @endphp

    @if(is_array($value))
        <div class="way-editor-item">
            <p class="way-editor-item-title">{{ $label }}</p>
            @error($fieldPath) <p class="text-danger small" role="alert">{{ $message }}</p> @enderror
            @if($prefix === 'content.values.items')
                @include('admin.partials.dinoyo_way_image', ['imageKey' => 'pillar_'.$key, 'slot' => $imageSlots['pillar_'.$key]])
            @endif
            @include('admin.partials.dinoyo_way_fields', ['fields' => $value, 'prefix' => $fieldPath])
        </div>
    @else
        @php
            $inputValue = old($fieldPath, $value);
            if (!is_scalar($inputValue)) $inputValue = $value;
            $multiline = (is_numeric($key) && $parent !== 'areas') || in_array($key, ['description', 'statement', 'lead', 'detail', 'behavior', 'note', 'fairness_note', 'closing']);
        @endphp
        <div class="mb-3">
            <label for="{{ $fieldId }}" class="form-label">{{ $label }} <span class="text-danger" aria-hidden="true">*</span></label>
            @if(is_int($value))
                <input type="number" min="0" max="100" step="1" name="{{ $inputName }}" id="{{ $fieldId }}" value="{{ $inputValue }}" class="form-control @error($fieldPath) is-invalid @enderror" required @if($errors->has($fieldPath)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif>
            @elseif($multiline)
                <textarea name="{{ $inputName }}" id="{{ $fieldId }}" rows="3" maxlength="{{ \App\Services\DinoyoWayContent::maxLength($fieldPath) }}" class="form-control @error($fieldPath) is-invalid @enderror" required @if($errors->has($fieldPath)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif>{{ $inputValue }}</textarea>
            @else
                <input type="text" name="{{ $inputName }}" id="{{ $fieldId }}" value="{{ $inputValue }}" maxlength="{{ \App\Services\DinoyoWayContent::maxLength($fieldPath) }}" class="form-control @error($fieldPath) is-invalid @enderror" required @if($errors->has($fieldPath)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif>
            @endif
            @if(str_ends_with((string) $key, '_url'))
                <p class="form-text">Gunakan https://..., path lokal seperti /contact, atau anchor seperti #nilai.</p>
            @endif
            @error($fieldPath) <div id="{{ $fieldId }}-error" class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endif
@endforeach
