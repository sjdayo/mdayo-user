<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mdayo\User\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
   
        $user = User::firstOrCreate(['email' => config('user.default_admin.email')],[
                'name' => config('user.default_admin.name'),
                'password' => Hash::make(config('user.default_admin.password'))
        ]);
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);

        $permissions = $role->permissions->pluck('name');
        $user->givePermissionTo($permissions);
    }
}
