<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->createPermissions();
        $this->createRoles();


        $users = User::all();
        foreach($users as $user){
            $user->assignRole(4);
        }

        $admins = User::where("role","admin")->get();
        foreach($admins as $admin) {
            $admin->syncRoles(1);
        }

        $mods = User::where("role","moderator")->get();
        foreach($mods as $mod) {
            $mod->syncRoles(2);
        }

        $clients = User::where("role","client")->get();
        foreach($clients as $client) {
            $client->syncRoles(3);
        }




    }

    public function createPermissions()
    {
        foreach (config('permissions_web') as $name) {
            Permission::findOrCreate($name);
        }
    }

    //TODO run only once
    public function createRoles()
    {
        $userPermissions=[
            'user.server.create',
            'user.server.upgrade',
            'user.shop.buy',
            'user.ticket.read',
            'user.ticket.write',
            'user.referral',
        ];
        /** @var Role $adminRole */
        $adminRole = Role::updateOrCreate(["name"=>"Admin","color"=>"#fa0000", "power"=>100]);
        $supportRole = Role::updateOrCreate(["name"=>"Support-Team","color"=>"#00b0b3","power"=>50]);
        $clientRole = Role::updateOrCreate(["name"=>"Client","color"=>"#008009","power"=>10]);
        $userRole =  Role::updateOrCreate(["name"=>"User","color"=>"#0052a3","power"=>10]);

        $adminRole->givePermissionTo(Permission::findByName('*'));

        $userRole->syncPermissions($userPermissions);
        $clientRole->syncPermissions($userPermissions);
    }
}
