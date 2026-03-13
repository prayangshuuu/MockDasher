<form method="POST" action="{{ url('/user/confirm-password') }}">
    @csrf
    Please confirm your password before continuing.
    <input type="password" name="password" required autofocus>
    <button type="submit">Confirm</button>
</form>
