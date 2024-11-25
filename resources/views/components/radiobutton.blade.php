@props([
    'name' => '',
    'id' => '',
    'label' => '',
    'value' => '',
    'options' => [], // ['yes' => 'Yes', 'no' => 'No']
    'selected' => '',
])

<div class="form-group mb-2">
    @if ($label)
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    @foreach ($options as $key => $optionLabel)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $id . '_' . $key }}" value="{{ $key }}" {{ $selected == $key ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $id . '_' . $key }}">
                {{ $optionLabel }}
            </label>
        </div>
    @endforeach

    <span class="invalid-feedback d-block" role="alert" id="{{ $name }}_error"></span>
</div>
