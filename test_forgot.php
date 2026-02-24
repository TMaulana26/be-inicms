<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

echo "Testing Forgot Password for: " . $user->email . "\n";

$status = Password::broker()->sendResetLink(['email' => $user->email]);

if ($status === Password::RESET_LINK_SENT) {
    echo "Success! Reset link was generated and dispatched via notification.\n";
} else {
    echo "Failed: $status\n";
}
