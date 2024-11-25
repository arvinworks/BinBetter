@props([
    'name',
    'id' => '',
    'label' => '',
    'value' => '',
    'placeholder' => 'Enter text',
    'rows' => 3
])

<div class="form-group mb-2">
    @if($label)
        <label for="{{ $id }}"  class="form-label">{{ $label }}</label>
    @endif

    <textarea 
        name="{{ $name }}" 
        id="{{ $id }}" 
        class="form-control @error($name) is-invalid @enderror"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
    >{{ old($name, $value) }}</textarea>

    <span class="invalid-feedback d-block" role="alert" id="{{ $name }}_error"></span>
</div>
