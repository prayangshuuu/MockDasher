<form method="POST" action="{{ url('/two-factor-challenge') }}">
    @csrf
    Please enter your authentication code:
    <input type="text" name="code" autofocus>
    <button type="submit">Verify</button>
</form>
