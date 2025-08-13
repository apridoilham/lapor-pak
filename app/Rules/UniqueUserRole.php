<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueUserRole implements ValidationRule
{
    private ?int $ignoreId;

    public function __construct(int $ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = User::where('email', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $user = $query->first();

        if (!$user) {
            return;
        }

        if ($user->hasRole('resident')) {
            $fail('Email sudah terdaftar sebagai Pelapor, silakan hapus data Pelapor terlebih dahulu.');
        }

        if ($user->hasRole(['admin', 'super-admin'])) {
            $fail('Email ini sudah terdaftar sebagai Admin.');
        }
    }
}