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

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'User 1',
                'username' => 'user_1',
                'password' => Hash::make('password'),
            ]
        );
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
