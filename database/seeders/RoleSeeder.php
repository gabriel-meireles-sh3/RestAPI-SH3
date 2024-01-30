<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $attendantRole = Role::create(['name' => 'attendent']);
        $suportRole = Role::create(['name' => 'suport']);
        $userRole = Role::create(['name' => 'user']);
    }
}
