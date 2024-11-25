<div>
    <label for="{{ $id }}">{{ $label }}</label>
    <input list="{{ $listId }}" id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}">
    <datalist id="{{ $listId }}">
        @foreach ($options as $option)
        <option value="{{ $option }}"></option>
        @endforeach
    </datalist>
</div>

<!-- usage -->


<!-- $options = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry'];

return view('your-view', compact('options'));

<x-datalist
    id="datalist-input"
    list-id="options"
    name="option"
    label="Choose a fruit:"
    placeholder="Start typing..."
    :options="$options"
/> -->