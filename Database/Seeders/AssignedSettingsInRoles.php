<?php

namespace Modules\Iprofile\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Iprofile\Entities\Setting;


class AssignedSettingsInRoles extends Seeder
{

  public function run()
  {
    Model::unguard();

    $module = app('modules');
    $rolesRepository = app("Modules\Iprofile\Repositories\RoleApiRepository");
    $settingsRepository = app("Modules\Setting\Repositories\SettingRepository");
    $roles = $rolesRepository->getItemsBy();
    $data = [];

    $modulesWithSettings = $settingsRepository->moduleSettings($module->allEnabled());
    foreach ($modulesWithSettings as $key => $module) {
      $plainSettings[$key] = $settingsRepository->plainModuleSettings($key);
    }
    $settings = json_decode(json_encode($plainSettings));

    foreach ($roles as $role) {
      if (isset($role->slug) && $role->slug != 'super-admin') {
        foreach ($settings as $key => $setting) {
          $moduleName = strtolower($key);
          foreach ($setting as $key => $settingsModules) {
            if (isset($settingsModules) && $settingsModules->onlySuperAdmin == false) {
              $settingName = $key;
              $settingAssigned = $moduleName . '::' . $settingName;
              array_push($data, $settingAssigned);
            }
          }
        }
        $roleAssignedSettings = Setting::where('related_id', $role->id)
          ->where('entity_name', 'role')
          ->where('name', 'assignedSettings')
          ->first();

        if (!isset($roleAssignedSettings->id)) {
          Setting::create(
            [
              'related_id' => $role->id,
              'entity_name' => 'role',
              'name' => 'assignedSettings',
              'value' => $data
            ]
          );
        }
      }
    }
  }
}
