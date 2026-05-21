<?php

namespace App\Helpers;

use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    public static function rule(): Password
    {
        $rule = Password::min(
            setting('security_min_password_length', 8)
        );

        if (setting('security_require_special_char', false)) {
            $rule->symbols();
        }

        return $rule;
    }
}
