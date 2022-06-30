<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Hash::make('1234');

        $userOne = User::factory()->create(
            [
                'email' => 'user@mail.com',
                'password' => $password,
            ]
        );
        News::factory()
            ->count(5)
            ->for($userOne)
            ->create();

        $userTwo = User::factory()->create(
            [
                'password' => $password,
            ]
        );
        News::factory()
            ->count(5)
            ->for($userTwo)
            ->create();

    }
}
