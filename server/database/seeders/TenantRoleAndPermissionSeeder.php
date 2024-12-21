<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TenantRoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'can export', 'core' => true],
            ['core' => true, 'name' => 'can view export button'],
            ['core' => true, 'name' => 'can import'],
            ['core' => true, 'name' => 'can crud nodes'],
            ['core' => true, 'name' => 'can crud data bus nodes'],
            ['core' => true, 'name' => 'can view nodes delete button'],
            ['core' => true, 'name' => 'can view nodes edit button'],
            ['core' => true, 'name' => 'can view nodes edit or create form'],
            ['core' => true, 'name' => 'can view nodes data table'],
            ['core' => true, 'name' => 'can crud permissions',],
            ['core' => true, 'name' => 'can view permissions delete button'],
            ['core' => true, 'name' => 'can view permissions edit button'],
            ['core' => true, 'name' => 'can view permissions edit or create form'],
            ['core' => true, 'name' => 'can view permissions data table'],
            ['core' => true, 'name' => 'can crud roles',],
            ['core' => true, 'name' => 'can view roles delete button'],
            ['core' => true, 'name' => 'can view roles edit button'],
            ['core' => true, 'name' => 'can view roles edit or create form'],
            ['core' => true, 'name' => 'can view roles data table'],
            ['core' => true, 'name' => 'can crud users',],
            ['core' => true, 'name' => 'can view users delete button'],
            ['core' => true, 'name' => 'can edit users password'],
            ['core' => true, 'name' => 'can view users edit button'],
            ['core' => true, 'name' => 'can view users edit form'],
            ['core' => true, 'name' => 'can view users data table'],
            ['core' => true, 'name' => 'can view users assign roles button'],
            ['core' => true, 'name' => 'can clear cache',],
            ['core' => true, 'name' => 'can crud settings',],
            ['core' => true, 'name' => 'can view settings delete button'],
            ['core' => true, 'name' => 'can view settings edit or create form'],
            ['core' => true, 'name' => 'can view settings data table'],
            ['core' => true, 'name' => 'can crud tenant',],
            ['core' => true, 'name' => 'can view audit history dashboard component',],
            ['core' => true, 'name' => 'can view new users dashboard component',],
            ['core' => true, 'name' => 'can view last update api route dashboard component',],
            ['core' => true, 'name' => 'can create or update references',],
            ['core' => true, 'name' => 'can view references',],
            ['core' => true, 'name' => 'can crud redirects',],
            ['core' => true, 'name' => 'can view redirects',],
            ['core' => true, 'name' => 'can edit redirects',],
            ['core' => true, 'name' => 'can delete redirects',],
            ['core' => true, 'name' => 'can create redirects',],
        ];

        // $role = ' api owner';
        // $role = Role::create(['name' => $role]);
        $super_admin = Role::create(['name' => "Super Admin", 'core' => true]);

        foreach ($permissions as $permission) {
            $permission = Permission::create($permission);
            // $role->givePermissionTo($permission->name);
            $super_admin->givePermissionTo($permission->name);
        }

        Setting::updateOrCreate(['key' => 'admin_role'], ['properties' => $super_admin->name . '_' . $super_admin->id]);

        $super_admin_user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@test.com',
                    'password' => Hash::make('admin123')
        ]);
        $super_admin_user->assignRole($super_admin);
    }

}
