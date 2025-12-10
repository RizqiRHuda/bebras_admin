<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat roles
        $roles = [
            ['name' => 'admin'],
            ['name' => 'user'],
            ['name' => 'biro'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Ambil role
        $adminRole = Role::where('name', 'admin')->first();
        $userRole  = Role::where('name', 'user')->first();

        // 2. Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('password'),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // OPTIONAL: Buat user biasa
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'User',
                'username' => 'user',
                'email'    => 'user@gmail.com',
                'password' => Hash::make('password'),
            ]
        );
        $user->roles()->sync([$userRole->id]);
    }

}
