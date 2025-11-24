<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <script>
        // Close the popup and redirect the parent window
        window.onload = function() {
            // If this is a popup and has an opener (parent window)
            if (window.opener) {
                // Send message to parent window
                window.opener.postMessage({
                    type: 'oauth-callback',
                    status: '{{ $status }}',
                    message: '{{ $message }}',
                    redirectUrl: '{{ $redirectUrl }}'
                }, window.location.origin);
                
                // Close the popup
                window.close();
            } else {
                // If not in a popup, just redirect
                window.location.href = '{{ $redirectUrl }}';
            }
        };
    </script>
</head>
<body>
    <div style="text-align: center; padding: 2rem;">
        <p>Redirecting...</p>
    </div>
</body>
</html>
