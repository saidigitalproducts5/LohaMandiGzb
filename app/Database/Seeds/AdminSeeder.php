<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $roles = [
            'super_admin' => 1,
            'admin'       => 2,
            'moderator'   => 4,
        ];

        $admin = [
            'role_id'    => $roles['super_admin'],
            'name'       => 'Super Admin',
            'email'      => 'admin@lohamandi.com',
            'mobile'     => '9999999999',
            'password'   => password_hash('Admin@123', PASSWORD_DEFAULT),
            'status'     => 'active',
            'email_verified' => 1,
            'mobile_verified' => 1,
        ];

        if ($db->table('users')->where('email', $admin['email'])->countAllResults() === 0) {
            $db->table('users')->insert($admin);
            echo "Admin user created: admin@lohamandi.com / Admin@123" . PHP_EOL;
        } else {
            echo "Admin user already exists." . PHP_EOL;
        }

        $moderator = [
            'role_id'    => $roles['moderator'],
            'name'       => 'Moderator',
            'email'      => 'moderator@lohamandi.com',
            'mobile'     => '9999999998',
            'password'   => password_hash('Mod@123', PASSWORD_DEFAULT),
            'status'     => 'active',
            'email_verified' => 1,
            'mobile_verified' => 1,
        ];

        if ($db->table('users')->where('email', $moderator['email'])->countAllResults() === 0) {
            $db->table('users')->insert($moderator);
            echo "Moderator user created: moderator@lohamandi.com / Mod@123" . PHP_EOL;
        }
    }
}


