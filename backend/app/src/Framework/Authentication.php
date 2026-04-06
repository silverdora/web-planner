<?php

namespace App\Framework;

final class Authentication
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
