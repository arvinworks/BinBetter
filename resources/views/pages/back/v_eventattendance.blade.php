@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0">
    <div class="bg-primary pt-12 pb-21 "></div>
    <div class="container-fluid mt-n22 ">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0  text-white">{{ $page }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-xl-12 col-12 mb-5">
                <div class="card mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">List of Event Attendance data.</h4>
                        </div>

                        <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                            Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                        </button>
                    </div>

                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-eventattendance-table'])
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for QR Scanner -->
    <div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scannerModalLabel">Scan QR Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video id="preview" style="width: 100%;"></video>
                    <div id="scan-result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script src="{{ route('secure.js', ['filename' => 'eventattendance']) }}"></script>
<script>
    $('.close').on('click', function() {
        $('#scannerModal').modal('hide');
    });

    const scanResult = document.getElementById('scan-result');
    const preview = document.getElementById('preview');
    const addButton = document.getElementById('add-btn');

    let videoStream = null;
    let scanning = false;

    // Start scanning when the 'Add' button is clicked
    addButton.addEventListener('click', function() {
        $('#scannerModal').modal('show'); // Show the modal
        scanResult.innerHTML = ''; // Clear any previous result

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Access the camera
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                })
                .then(function(stream) {
                    videoStream = stream;
                    preview.srcObject = stream;
                    preview.play();

                    // Wait for the video to load and be ready
                    preview.onloadedmetadata = function() {
                        // Start scanning once the video dimensions are available
                        scanning = true;
                        scanQRCode();
                    };
                })
                .catch(function(error) {
                    scanResult.innerHTML = `<div class="alert alert-danger">Error accessing the camera: ${error.message}</div>`;
                });

        } else {
            scanResult.innerHTML = `<div class="alert alert-danger">Camera not supported on this device.</div>`;
        }
    });

    // QR Code scanning function
    function scanQRCode() {
        if (!scanning) return;

        // Ensure the video has valid dimensions
        if (preview.videoWidth === 0 || preview.videoHeight === 0) {
            requestAnimationFrame(scanQRCode);
            return;
        }

        // Draw the current frame to the canvas
        const canvas = document.createElement("canvas");
        const context = canvas.getContext("2d");
        canvas.width = preview.videoWidth;
        canvas.height = preview.videoHeight;
        context.drawImage(preview, 0, 0, canvas.width, canvas.height);

        // Try to decode the QR code
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);

        if (code) {
            scanResult.innerHTML = `<div class="alert alert-success">QR Code Detected: ${code.data}</div>`;
            // Stop scanning
            scanning = false;

            // You can perform further actions with the QR code data here
            processQRCode(code.data);
        } else {
            // Keep scanning if no code was found
            requestAnimationFrame(scanQRCode);
        }
    }

    // Handle the QR code data (for example, making an API request)
    function processQRCode(data) {
        const url = new URL(data);
        const paths = url.pathname.split('/');
        const jeid = paths[2];
        const userid = paths[3];
        const eventid = paths[4];

        fetch(`/event-scan/${jeid}/${userid}/${eventid}`)
            .then(response => response.json())
            .then(data => {
                if (data.type === 'success') {
                    scanResult.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                } else if (data.type === 'info') {
                    scanResult.innerHTML = `<div class="alert alert-info">${data.message}</div>`;
                } else {
                    scanResult.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scanResult.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
            });
    }

    // Stop the camera when closing the modal
    $('#scannerModal').on('hidden.bs.modal', function() {
        if (videoStream) {
            let tracks = videoStream.getTracks();
            tracks.forEach(track => track.stop());
        }
        scanning = false;
    });
</script>
@endpush