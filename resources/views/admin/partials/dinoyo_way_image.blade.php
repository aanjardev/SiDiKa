<div class="way-editor-image-row">
    <img class="way-editor-image" src="{{ $slot['url'] }}" alt="{{ $slot['label'] }} saat ini" loading="lazy">
    <div>
        <label for="way-photo-{{ $imageKey }}" class="form-label">{{ $slot['label'] }}</label>
        <input type="file" name="page_images[{{ $imageKey }}]" id="way-photo-{{ $imageKey }}" accept="image/jpeg,image/png,image/webp" class="form-control @error('page_images.'.$imageKey) is-invalid @enderror" aria-describedby="way-photo-{{ $imageKey }}-help">
        @error('page_images.'.$imageKey)<div class="invalid-feedback">{{ $message }}</div>@enderror
        <p class="form-text" id="way-photo-{{ $imageKey }}-help">JPG, PNG, atau WebP. Maksimal 4 MB dan 5000 x 5000 piksel. Kosongkan untuk mempertahankan foto saat ini.</p>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="reset_page_images[{{ $imageKey }}]" value="1" id="way-reset-{{ $imageKey }}" @checked(old('reset_page_images.'.$imageKey))>
            <label for="way-reset-{{ $imageKey }}" class="form-check-label small">Gunakan kembali foto awal</label>
        </div>
        @error('reset_page_images.'.$imageKey)<p class="text-danger small">{{ $message }}</p>@enderror
    </div>
</div>
