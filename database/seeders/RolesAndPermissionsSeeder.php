<?php

namespace Database\Seeders;

use App\Models\Badge;
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
            // 'role' => [['permissions'], ['Badge description', 'Badge image']],
            'user' => [[], ['A real gamer', null]],
            'admin' => [
                ['user', 'role', 'permission', 'filament'],
                ['Administrator of the website', null],
            ],
            'alpha tester' => [[], ['Someone who helped test in the alpha', null]],
        ];

        foreach ($generateData as $roleName => $data) {
            $this->command->info("Seeding role: $roleName");
            $role = Role::findOrCreate($roleName);

            $permissions = $data[0];
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

            $badgeData = $data[1];
            $badge = Badge::updateOrCreate(
                ['role_id' => $role->id],
                [
                    'description' => $badgeData[0],
                    'image' => $badgeData[1],
                ]
            );

            $this->command->info("Created badge for role: $role->name");
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
