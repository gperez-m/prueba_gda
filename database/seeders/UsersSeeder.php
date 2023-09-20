<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'email' => 'admin@admin.mx',
            'email_verified_at' => \Carbon\Carbon::now(),
            'password' => Hash::make('Admin1234'),
            'name'=>'John',
            'created_at'=>\Carbon\Carbon::now()
        ]);

        DB::table('regions')->insert([
            'description' => 'AMERICA', 
            'status' => 'A'
        ]);

        DB::table('communes')->insert([
            'id_reg' => 1,
            'description' => 'Communes 1'
        ]);

        DB::table('customers')->insert([
            'dni' => 'admin@admin.mx',
            'id_reg' => 1,
            'id_com' => 1,
            'email' => 'admin@admin.mx',
            'name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main Street',
            'data_reg' => \Carbon\Carbon::now(),
            'status' => 'A'
        ]);
    }
}
