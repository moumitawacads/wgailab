<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AddMoreAdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plainPassword = "urzadmin@1";
        $usersList = [
            // [
            //     'name' => 'Vince Rozario',
            //     'email' => 'vince@urbanrezsolutions.com', // Change this to your email
            //     'password' => Hash::make($plainPassword), // Change this
            //     'phone' => '9876543210',
            //     'role' => 'superadmin',
            // ],
            // [
            //     'name' => 'Sacha Williams',
            //     'email' => 'sacha@urbanrezsolutions.com', // Change this to your email
            //     'password' => Hash::make($plainPassword), // Change this
            //     'phone' => '9876543211',
            //     'role' => 'superadmin',
            // ],
            // [
            //     'name' => 'Rochelle Allen',
            //     'email' => 'rochelle@urbanrezsolutions.com', // Change this to your email
            //     'password' => Hash::make($plainPassword), // Change this
            //     'phone' => '9876543212',
            //     'role' => 'superadmin',
            // ],
            // [
            //     'name' => 'Marlene Henry-Smith',
            //     'email' => 'Marlene@urbanrezsolutions.com', // Change this to your email
            //     'password' => Hash::make($plainPassword), // Change this
            //     'phone' => '9876543213',
            //     'role' => 'superadmin',
            // ],
            // [
            //     'name' => 'Farley Flex Nwaigbo',
            //     'email' => 'farley@urbanrezsolutions.com', // Change this to your email
            //     'password' => Hash::make($plainPassword), // Change this
            //     'phone' => '9876543214',
            //     'role' => 'superadmin',
            // ],
            [
                'name' => 'Roderick Brereton',
                'email' => 'rod@urbanrezsolutions.com', // Change this to your email
                'password' => Hash::make($plainPassword), // Change this
                'phone' => '9876543215',
                'role' => 'superadmin',
            ],
        ];

        foreach ($usersList as $userList) {
            $user = User::create($userList);

            Mail::send('admin.emails.user_credentials', [
                'user' => $user,
                'password' => $plainPassword
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Login Credentials');
            });
        }
    }
}
