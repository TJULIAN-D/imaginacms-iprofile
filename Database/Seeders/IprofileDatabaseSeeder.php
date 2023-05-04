<?php

namespace Modules\Iprofile\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Iprofile\Database\Seeders\IformUserDefaultRegisterTableSeeder;
use Modules\Isite\Jobs\ProcessSeeds;

class IprofileDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Iprofile\Database\Seeders",
      "seeds" => ["IprofileModuleTableSeeder", "DepartmentTableSeeder", "UserDepartmentTableSeeder",
        "RolePermissionsSeeder", "RolePermissionsToAccessSeeder", "IformUserDefaultRegisterTableSeeder",
        "AssignedSettingsInRoles"]
    ]);
  }
}
