<form method="POST" action="{{ route('verification.send') }}">
    @csrf

    <button type="submit" class="btn btn-primary">
        {{ __('Resend Verification Email') }}
    </button>
</form>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success" role="alert">
        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
    </div>
@endif
