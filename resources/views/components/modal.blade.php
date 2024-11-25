<!-- Modal Component -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog {{ $size ?? '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($confirmText != '')
                <button type="button" class="btn btn-primary" id="{{ $confirmButtonId }}">
                    <span class="{{ $confirmButtonId }}_button_text"> {{ $confirmText }} </span>
                    <span class="{{ $confirmButtonId }}_load_data d-none">Loading <i class="loader"></i></span>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>