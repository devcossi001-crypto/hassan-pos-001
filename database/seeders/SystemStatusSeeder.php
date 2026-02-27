<?php

namespace Database\Seeders;

use App\Models\SystemStatus;
use Illuminate\Database\Seeder;

class SystemStatusSeeder extends Seeder
{
    public function run(): void
    {
        // Create initial system status record if it doesn't exist
        SystemStatus::firstOrCreate(
            [],
            [
                'is_active' => true,
                'status_reason' => null,
                'deactivated_by' => null,
                'deactivated_at' => null,
                'activated_at' => now(),
            ]
        );
    }
}
