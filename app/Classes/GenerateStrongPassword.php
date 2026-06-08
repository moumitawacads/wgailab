<?php

namespace App\Classes;

class GenerateStrongPassword
{
    public function run()
    {
        $sets = [];
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        $sets[] = '23456789';
        $sets[] = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $all = '';
        $password = '';

        // Get at least one character from each set
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        // Fill the rest with random characters from all sets
        $length = 8; // minimum length
        while (strlen($password) < $length) {
            $password .= $all[array_rand(str_split($all))];
        }

        // Shuffle the password so the required characters aren't just at the beginning
        $password = str_shuffle($password);

        return $password;
    }
}
