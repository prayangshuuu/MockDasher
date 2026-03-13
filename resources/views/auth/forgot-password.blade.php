@if (session('status'))
    <div>{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    Email: <input type="email" name="email" required>
    <button type="submit">Email Password Reset Link</button>
</form>
