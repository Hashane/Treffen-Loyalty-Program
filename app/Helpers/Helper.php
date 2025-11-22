<?php

namespace App\Helpers;

class Helper
{
    public function parseFullName(string $fullName): array
    {
        $parts = array_filter(explode(' ', trim($fullName)));

        if (count($parts) === 1) {
            return [
                'first_name' => $parts[0],
                'last_name' => $parts[0],
            ];
        }

        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }
}
