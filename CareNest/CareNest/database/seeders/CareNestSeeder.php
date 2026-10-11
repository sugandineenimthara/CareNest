<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CareNestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Login User Accounts
        User::firstOrCreate(
            ['email' => 'admin@carenest.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin123'),
                'role' => 'provider',
            ]
        );

        // 2. Create the 5 exact Clinic Areas requested
        $areas = [
            'Kala-Eliya',
            'Indivitiya',
            'Wewala',
            'Kapuwaththa-01',
            'Kapuwaththa-02',
        ];

        foreach ($areas as $areaName) {
            $area = Area::firstOrCreate(['area_name' => $areaName]);
            
            // Assign a midwife on duty for each area
            Midwife::firstOrCreate(
                ['area_id' => $area->area_id],
                ['midwife_name' => 'Midwife (' . $areaName . ')']
            );
        }
    }
}
