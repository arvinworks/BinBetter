@props([
'name',
'id' => '',
'label' => '',
'options' => [],
'selected' => null,
'placeholder' => 'Select an option'
])

<div class="form-group mb-2 {{ 
    $label === 'Menu' ? 'hide_formgroup_menu' : 
    ($label === 'Report Type' ? 'hide_formgroup_rtype' : 
    ($label === 'Event Status' ? 'hide_formgroup_status' : '')) 
}}">
    @if($label)
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <select name="{{ $name }}" id="{{ $id }}" class="form-control @error($name) is-invalid @enderror" {{ $attributes }}>
        <option value="" disabled {{ is_null(old($name, $selected)) ? 'selected' : '' }}>
            {{ $placeholder }}
        </option>
        @foreach($options as $value => $text)
        <option value="{{ $value }}" {{ $value == old($name, $selected) ? 'selected' : '' }}>
            {{ $text }}
        </option>
        @endforeach
    </select>

    @if($name === 'layout')
    <div id="layout-sample">
        <div class="row  mb-2 p-2" id="showcol12">
            <h6 class="text-center text-uppercase"><b>Column 12 sample</b></h6>
            <div class="col-12 h-25 bg-light p-2">
                <h6>Column 12</h6>
            </div>
        </div>

        <div class="row d-none mb-2 p-2" id="showcol8to4">

            <h6 class="text-center text-uppercase"><b>Column 8 to 4 sample</b></h6>
            <div class="col-8 h-25 bg-light p-2">
                <h6>Column 8</h6>
            </div>
            <div class="col-4 h-25 bg-secondary p-2">
                <h6 class="text-white">Column 4</h6>
            </div>
        </div>

        <div class="row d-none mb-2 p-2" id="showcol7to5">
            <h6 class="text-center text-uppercase"><b>Column 7 to 5 sample</b></h6>
            <div class="col-8 h-25 bg-light p-2">
                <h6>Column 7</h6>
            </div>
            <div class="col-4 h-25 bg-secondary p-2">
                <h6 class="text-white">Column 5</h6>
            </div>
        </div>

        <div class="row d-none mb-2 p-2" id="showcol6to6">

            <h6 class="text-center text-uppercase"><b>Column 6 to 6 sample</b></h6>

            <div class="col-6 h-25 bg-light p-2">
                <h6>Column 6</h6>
            </div>
            <div class="col-6 h-25 bg-secondary p-2">
                <h6 class="text-white">Column 6</h6>
            </div>
        </div>

        <div class="row d-none mb-2 p-2" id="showcol4">

            <h6 class="text-center text-uppercase"><b>Column 4x3 sample</b></h6>

            <div class="col-4 h-25 bg-light p-2">
                <h6>Column 4</h6>
            </div>
            <div class="col-4 h-25 bg-secondary p-2">
                <h6 class="text-white">Column 4</h6>
            </div>
            <div class="col-4 h-25 bg-primary p-2">
                <h6 class="text-white">Column 4</h6>
            </div>

            <p class="mt-1"><b>Note:</b> <small><i>Need to create 3 col-4 for this layout</i></small></p>
        </div>

        <div class="row d-none mb-2 p-2" id="showcol3">

            <h6 class="text-center text-uppercase"><b>Column 3x3 sample</b></h6>

            <div class="col-3 h-25 bg-light p-2">
                <h6>Column 3</h6>
            </div>
            <div class="col-3 h-25 bg-secondary p-2">
                <h6 class="text-white">Column 3</h6>
            </div>
            <div class="col-3 h-25 bg-primary p-2">
                <h6 class="text-white">Column 3</h6>
            </div>
            <div class="col-3 h-25 bg-dark p-2">
                <h6 class="text-white">Column 3</h6>
            </div>

            <p class="mt-1"><b>Note:</b> <small><i>Need to create 4 col-3 for this layout</i></small></p>
        </div>
    </div>

    @endif

    <span class="invalid-feedback d-block" role="alert" id="{{ $name }}_error"></span>
</div>