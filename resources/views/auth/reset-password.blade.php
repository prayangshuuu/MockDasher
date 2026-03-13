<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    Email: <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
    Password: <input type="password" name="password" required>
    Confirm Password: <input type="password" name="password_confirmation" required>
    <button type="submit">Reset Password</button>
</form>
