<?php

return [
    // Authentication messages
    'login_success' => 'Login successful',
    'register_success' => 'Registration successful',
    'logout_success' => 'Successfully logged out',
    'password_changed' => 'Password changed successfully',
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'invalid_credentials' => 'Invalid credentials',
    'email_in_use' => 'Email already in use',
    'invalid_current_password' => 'The current password is incorrect',
    'forgot_password' => 'Forgot password?',
    'remember_me' => 'Remember me',
    
    // Login page
    'login' => [
        'title' => 'Welcome Back',
        'subtitle' => 'Enter your credentials to access your account',
        'email' => 'Email address',
        'password' => 'Password',
        'login_button' => 'Sign In',
        'no_account' => "Don't have an account?",
        'forgot_password' => 'Forgot password?',
        'sign_up' => 'Sign up',
        'logging_in' => 'Logging in...'
    ],
    
    // Google Sign In
    'continue_with_google' => 'Continue with Google',
    'or_sign_in_with_email' => 'Or sign in with email',
    'or_sign_up_with_email' => 'Or sign up with email',
    
    // Common
    'email' => 'Email',
    'name' => 'Full Name',
    'confirm_password' => 'Confirm Password',
    'already_registered' => 'Already registered?',
    'sign_in' => 'Sign in',
    
    // Register page
    'register' => [
        'title' => 'Create Your Account',
        'subtitle' => 'Join MK Scholars Driving School today!',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'sign_up' => 'Sign up',
        'creating_account' => 'Creating account...',
        'phone' => 'Phone Number',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'language' => 'Preferred Language',
        'register_button' => 'Create Account',
        'have_account' => 'Already have an account?',
        'sign_in' => 'Sign in'
    ],
    
    // Errors
    'errors' => [
        'invalid_credentials' => 'Invalid email or password',
        'email_exists' => 'An account with this email already exists. Please log in instead.',
        'password_mismatch' => 'Passwords do not match',
        'weak_password' => 'Password must be at least 6 characters',
        'invalid_email' => 'Please enter a valid email address'
    ],
    
    // Password validation messages
    'password_requirements' => [
        'title' => 'Password must contain:',
        'length' => 'At least 8 characters',
        'uppercase' => 'At least 1 uppercase letter',
        'lowercase' => 'At least 1 lowercase letter',
        'number' => 'At least 1 number',
        'special' => 'At least 1 special character (!@#$%^&*)',
        'strength' => [
            'weak' => 'Weak',
            'medium' => 'Medium',
            'strong' => 'Strong',
            'very_strong' => 'Very Strong'
        ],
        'match' => 'Passwords match',
        'mismatch' => 'Passwords do not match'
    ],

    // Forgot Password page
    'forgot_password_page' => [
        'title' => 'Reset Your Password',
        'subtitle' => 'Enter your email address and we\'ll send you a link to reset your password.',
        'email_placeholder' => 'Email address',
        'submit_button' => 'Send Reset Link',
        'back_to_login' => 'Back to login',
        'success_message' => 'We have emailed your password reset link!',
        'error_message' => 'We can\'t find a user with that email address.'
    ],
];