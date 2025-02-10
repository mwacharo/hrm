<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class ViewTeamLeavesSeeder extends Seeder
{
    public function run()
    {
        $viewTeamLeavesPermission = Permission::findOrCreate('view_team_leaves');
        $role = Role::findByName('employee');
        $users = User::role($role->name)->where('designation_id', 1)->get();

        foreach ($users as $user) {
            $user->givePermissionTo($viewTeamLeavesPermission);
        }
    }
}
