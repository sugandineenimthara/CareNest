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
        User::updateOrCreate(
            ['email' => 'admin@carenest.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin123'),
                'role' => 'provider',
                'status' => 'approved',
            ]
        );

        $areas = [
            'Kala-Eliya',
            'Indivitiya',
            'Wewala',
            'Kapuwaththa-01',
            'Kapuwaththa-02',
        ];

        $firstArea = null;
        foreach ($areas as $index => $areaName) {
            $area = Area::firstOrCreate(['area_name' => $areaName]);
            if ($index === 0) {
                $firstArea = $area;
            }
        }

        $midwifeUser = User::updateOrCreate(
            ['email' => 'midwife@carenest.com'],
            [
                'name' => 'Midwife User',
                'password' => Hash::make('midwife123'),
                'role' => 'midwife',
                'status' => 'approved',
            ]
        );

        if ($firstArea) {
            Midwife::updateOrCreate(
                ['user_id' => $midwifeUser->id],
                [
                    'midwife_name' => 'Midwife User',
                    'area_id' => $firstArea->area_id,
                ]
            );
        }
    }
}
