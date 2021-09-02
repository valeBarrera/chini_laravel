<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->name = 'admin';
        $role->description = 'Administrador';
        $role->save();
        $role = new Role();
        $role->name = 'vendedor';
        $role->description = 'Vendedor';
        $role->save();
        $role = new Role();
        $role->name = 'user';
        $role->description = 'Usuario/Cliente';
        $role->save();

        $user = new User();
        $user->name = 'Guillermo Admin';
        $user->email = 'admin@gmail.com';
        $user->password = bcrypt('guillemo324');
        $user->save();
        $user->roles()->attach(Role::where('name', 'admin')->first());

        $user = new User();
        $user->name = 'Guillermo Vendedor';
        $user->email = 'vendedor@gmail.com';
        $user->password = bcrypt('guillemo324');
        $user->save();
        $user->roles()->attach(Role::where('name', 'vendedor')->first());
        $user = new User();

        $user->name = 'Guillermo Cliente';
        $user->email = 'cliente@gmail.com';
        $user->password = bcrypt('guillemo324');
        $user->save();
        $user->roles()->attach(Role::where('name', 'user')->first());

    }
}
