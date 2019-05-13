<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        # Genera el usuario Admin por defecto cada vez que migremos la base de datos (en limpio)
        factory(\App\User::class)->create(['name' => "Admin", 'email' => "admin@admin.com", 'password' => bcrypt('admin')]);
    }
}
