@extends('layouts.back.app')

@section('content')
<div class="row align-items-center justify-content-center g-0 min-vh-100">
    <div class="col-12 col-md-8 col-lg-6 col-xxl-4 py-8 py-xl-0">

        @include('components.toggle-theme')

        <!-- Card -->
        <div class="card smooth-shadow-md">
            <!-- Card body -->
            <div class="card-body p-5">
                <div class="mb-4">
                    <a href="{{ url('/') }}"><img src="{{ asset('assets/back/images/brand/logo/logo.png') }}" class="mb-2 text-inverse" alt="Image" /></a>
                    <p class="mb-6">Don't worry, we'll send you an email to reset your password.</p>
                </div>
                <!-- Form -->
                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" name="email" placeholder="Enter Your Email" value="{{ old('email') }}" autocomplete="email" autofocus />
                        <span class="invalid-feedback d-block" role="alert" id="email_error"></span>
                    </div>
                    <!-- Button -->
                    <div class="mb-3 d-grid">
                        <button type="button" class="btn btn-primary" id="forgotAccount">{{ __('Send Password Reset Link') }}</button>
                    </div>
                    <span>
                        Don't have an account?
                        <a href="{{ route('login') }}">sign in</a>
                    </span>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'forgot']) }}"></script>
@endpush