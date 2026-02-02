<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define initial permissions and roles (sync with existing permissions table)
        $permissions = [
            'ViewAny:Role',
            'View:Role',
            'Create:Role',
            'Update:Role',
            'Delete:Role',
            'Restore:Role',
            'ForceDelete:Role',
            'ForceDeleteAny:Role',
            'RestoreAny:Role',
            'Replicate:Role',
            'Reorder:Role',
            'ViewAny:Author',
            'View:Author',
            'Create:Author',
            'Update:Author',
            'Delete:Author',
            'Restore:Author',
            'ForceDelete:Author',
            'ForceDeleteAny:Author',
            'RestoreAny:Author',
            'Replicate:Author',
            'Reorder:Author',
            'ViewAny:Categories',
            'View:Categories',
            'Create:Categories',
            'Update:Categories',
            'Delete:Categories',
            'Restore:Categories',
            'ForceDelete:Categories',
            'ForceDeleteAny:Categories',
            'RestoreAny:Categories',
            'Replicate:Categories',
            'Reorder:Categories',
            'ViewAny:DocumentType',
            'View:DocumentType',
            'Create:DocumentType',
            'Update:DocumentType',
            'Delete:DocumentType',
            'Restore:DocumentType',
            'ForceDelete:DocumentType',
            'ForceDeleteAny:DocumentType',
            'RestoreAny:DocumentType',
            'Replicate:DocumentType',
            'Reorder:DocumentType',
            'ViewAny:Faculty',
            'View:Faculty',
            'Create:Faculty',
            'Update:Faculty',
            'Delete:Faculty',
            'Restore:Faculty',
            'ForceDelete:Faculty',
            'ForceDeleteAny:Faculty',
            'RestoreAny:Faculty',
            'Replicate:Faculty',
            'Reorder:Faculty',
            'ViewAny:StudyProgram',
            'View:StudyProgram',
            'Create:StudyProgram',
            'Update:StudyProgram',
            'Delete:StudyProgram',
            'Restore:StudyProgram',
            'ForceDelete:StudyProgram',
            'ForceDeleteAny:StudyProgram',
            'RestoreAny:StudyProgram',
            'Replicate:StudyProgram',
            'Reorder:StudyProgram',
            'ViewAny:Degree',
            'View:Degree',
            'Create:Degree',
            'Update:Degree',
            'Delete:Degree',
            'Restore:Degree',
            'ForceDelete:Degree',
            'ForceDeleteAny:Degree',
            'RestoreAny:Degree',
            'Replicate:Degree',
            'Reorder:Degree',
            'ViewAny:ProgramType',
            'View:ProgramType',
            'Create:ProgramType',
            'Update:ProgramType',
            'Delete:ProgramType',
            'Restore:ProgramType',
            'ForceDelete:ProgramType',
            'ForceDeleteAny:ProgramType',
            'RestoreAny:ProgramType',
            'Replicate:ProgramType',
            'Reorder:ProgramType',
            'ViewAny:TriDharma',
            'View:TriDharma',
            'Create:TriDharma',
            'Update:TriDharma',
            'Delete:TriDharma',
            'Restore:TriDharma',
            'ForceDelete:TriDharma',
            'ForceDeleteAny:TriDharma',
            'RestoreAny:TriDharma',
            'Replicate:TriDharma',
            'Reorder:TriDharma',
            'ViewAny:User',
            'View:User',
            'Create:User',
            'Update:User',
            'Delete:User',
            'Restore:User',
            'ForceDelete:User',
            'ForceDeleteAny:User',
            'RestoreAny:User',
            'Replicate:User',
            'Reorder:User',
            'View:Dashboard',
            'View:TriDharmaPerYear',
            'View:TriDharmaStatsOverview',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Give all defined permissions to super_admin
        $superAdminRole->syncPermissions(Permission::whereIn('name', $permissions)->get());

        // Create default admin user (env-backed credentials recommended)
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Administrator',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole($superAdminRole);
        }
    }
}
