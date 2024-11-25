@props([
    'type' => 'text',
    'name' => '',
    'id' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'accept' => '',
    'multiple' => false
])

<div class="form-group mb-2 {{ $label === 'Name' || $label === 'Url' ? 'hide_formgroup_menu' : '' }}">
    @if($type === 'checkbox')
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input p-2" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" @if ($required) required @endif>
            <label class="form-check-label fs-4 mx-1 lead" for="{{ $id }}">{{ $label }}</label>
        </div>
    @else
        @if ($label)
            <label for="{{ $id }}" class="form-label">{{ $label }}
                @if($name === 'photo' || $name === 'banner_photo' || $name === 'wcu_icon' || $name === 'profile')
                    <i class="d-none showNote"><small class="fw-normal">( Empty input default photo will be uploaded )</small></i>
                @endif
            </label>
        @endif

        <input type="{{ $type }}" class="form-control" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" @if ($required) required @endif @if ($readonly) readonly @endif @if ($accept) accept="{{ $accept }}" @endif      @if ($multiple) multiple @endif >
    @endif

    @if($name === 'icon')
        <i><small>Better for col-3 and col-4 layout fixed top position</small></i>
    @endif

    @if ($label === 'Reward Expiration Value:')
      <small>Note: <i>This will be added to the current date upon the user's selection of a subscription.</i></small>
    @endif

    <span class="invalid-feedback d-block" role="alert" id="{{ $name }}_error"></span>
</div>
