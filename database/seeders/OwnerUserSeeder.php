<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerUserSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create the owner role
        $ownerRole = Role::where('name', 'owner')->first();

        if ($ownerRole) {
            // Create or update the owner user
            $owner = User::updateOrCreate(
                ['email' => 'lindarono04@gmail.com'],
                [
                    'name' => 'System Owner',
                    'email' => 'lindarono04@gmail.com',
                    'password' => Hash::make('22360010s'),
                    'phone' => null,
                    'is_active' => true,
                ]
            );

            // Assign owner role if not already assigned
            if (!$owner->hasRole('owner')) {
                $owner->roles()->sync([$ownerRole->id]);
            }

            $this->command->info('Owner user created/updated: lindarono04@gmail.com');
        } else {
            $this->command->error('Owner role not found. Please run RolePermissionSeeder first.');
        }
    }
}
