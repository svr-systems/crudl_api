<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                "name" => "ADMIN. SISTEMA"
            ],
            [
                "name" => "VISOR"
            ],
            [
                "name" => "USUARIO"
            ],
        ];

        DB::table("roles")->insert($data);
    }
}
