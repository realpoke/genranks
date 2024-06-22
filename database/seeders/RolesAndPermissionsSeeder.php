<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Seeding roles and permissions');

        $generateData = [
            'user' => [],
            'admin' => [
                'user',
                'role',
                'permission',
                'filament',
            ],
        ];

        foreach ($generateData as $role => $permissions) {
            $this->command->info("Seeding role: $role");
            $role = Role::findOrCreate($role);
            foreach ($permissions as $permission) {
                $this->command->info("Seeding permission: $permission");
                Permission::findOrCreate('viewAny:'.$permission);
                Permission::findOrCreate('view:'.$permission);
                Permission::findOrCreate('create:'.$permission);
                Permission::findOrCreate('update:'.$permission);
                Permission::findOrCreate('delete:'.$permission);
                Permission::findOrCreate('restore:'.$permission);
                Permission::findOrCreate('forceDelete:'.$permission);
                Permission::findOrCreate('set:'.$permission);

                $this->command->info("Giving permission to role: $role->name");
                $role->givePermissionTo(Permission::whereLike('name', $permission)->get());
            }
            $this->command->info("Done seeding role: $role->name");
        }

        if (User::whereEmail('admin@mail.com')->exists()) {
            return;
        }
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@mail.com';
        $user->password = 'password';
        $user->save();

        $user->assignRole('admin');
    }
}
