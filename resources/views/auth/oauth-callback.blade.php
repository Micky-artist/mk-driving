<!DOCTYPE html>
<html>
<head>
    <title>Login Successful</title>
    <script>
        // Close the popup immediately
        // The session is already established on the backend
        (function() {
            if (window.opener) {
                // Close immediately - the parent window will handle the refresh
                window.close();
            } else {
                // If not in a popup, just redirect
                window.location.href = '{{ $redirectUrl }}';
            }
        })();
    </script>
</head>
<body>
    <div style="text-align: center; padding: 2rem;">
        <p>Login successful! Closing...</p>
    </div>
</body>
</html>
