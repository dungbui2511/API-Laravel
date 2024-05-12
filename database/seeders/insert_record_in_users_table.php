<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class insert_record_in_users_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users=[
            [
                'name'=>'amit',
                'email'=>'amit@gmail.com',
                'password'=>bcrypt('123456'),
            ],
            [
                'name'=>'john',
                'email'=>'john@yopmail.com',
                'password'=>bcrypt('123456'),
            ]
            ];
            User::insert($users);
    }
}
