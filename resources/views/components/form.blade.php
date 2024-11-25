@props([
    'formId' => '',
    'actionUrl' => '',
    'method' => 'POST',
])

<form id="{{ $formId }}" action="{{ $actionUrl }}" method="{{ $method }}" >
    @csrf
    @method($method)
    {{ $slot }} <!-- This will render the form fields inserted in the parent component -->
</form>