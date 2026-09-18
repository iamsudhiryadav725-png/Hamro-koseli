<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa...</title>
</head>
<body style="font-family: sans-serif; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; background:#F4EAE1; color:#3A2A1F;">
    <div style="text-align:center;">
        <p>Redirecting you to eSewa to complete your payment&hellip;</p>
        <p style="font-size:12px; opacity:0.6;">If nothing happens, click the button below.</p>

        <form id="esewa-form" action="{{ $formUrl }}" method="POST">
            @foreach ($fields as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach
            <button type="submit" style="margin-top:12px; padding:10px 20px; border-radius:8px; border:none; background:#60BB46; color:#fff; font-weight:bold; cursor:pointer;">
                Continue to eSewa
            </button>
        </form>
    </div>

    <script>
        document.getElementById('esewa-form').submit();
    </script>
</body>
</html>