<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $userModel = config('user.model');
        $user = $userModel::firstOrCreate(['email' => config('user.default_admin.email')],[
                'name' => config('user.default_admin.name'),
                'password' => Hash::make(config('user.default_admin.password'))
        ]);
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->syncRoles($role);

        $permissions = $role->permissions->pluck('name')->toArray();
        $user->syncPermissions($permissions);
    }
}
