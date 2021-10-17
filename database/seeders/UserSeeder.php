<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                "name" => "ADMIN PRINCIPAL",
                "email" => "admin@svr.com",
                "password" => bcrypt("12345678"),
                "created_by_id" => 1,
                "updated_by_id" => 1,
                "created_at" => Carbon::now()->format("Y-m-d H:i:s"),
                "updated_at" => Carbon::now()->format("Y-m-d H:i:s"),
                "role_id" => 1,
            ]
        ];

        DB::table("users")->insert($data);
    }
}
