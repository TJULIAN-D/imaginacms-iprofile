<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;

//Model
use Modules\Core\Icrud\Transformers\CrudResource;
use Modules\Iprofile\Entities\Role;
use Modules\Iprofile\Repositories\RoleApiRepository;
use Modules\Iprofile\Entities\Setting;
use Illuminate\Http\Request;

class RoleApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Role $model, RoleApiRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }

  /**
   * Controller to create model
   *
   * @param Request $request
   * @return mixed
   */
  public function create(Request $request)
  {
    \DB::beginTransaction();
    try {
      //Get model data
      $modelData = $request->input('attributes') ?? [];
      //Get Parameters from request
      $params = $this->getParamsRequest($request);

      //Validate Request
      if (isset($this->model->requestValidation['create'])) {
        $this->validateRequestApi(new $this->model->requestValidation['create']($modelData));
      }

      //Create model
      $model = $this->modelRepository->create($modelData);

      //Create Settings
      if (isset($modelData["settings"]) &&
        (isset($params->permissions["profile.settings.create"]) && $params->permissions["profile.settings.create"])) {
        foreach ($modelData["settings"] as $settingName => $setting) {
          Setting::create([
            'related_id' => $model->id,
            'entity_name' => 'role',
            'name' => $settingName,
            'value' => $setting
          ]);
        }
      }

      //Response
      $response = ["data" => CrudResource::transformData($model)];
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = $status == 409 ? json_decode($e->getMessage()) :
        ['messages' => [['message' => $e->getMessage(), 'type' => 'error']]];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Controller to update model by criteria
   *
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function update($criteria, Request $request)
  {
    \DB::beginTransaction(); //DB Transaction
    try {
      //Get model data
      $modelData = $request->input('attributes') ?? [];
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //auto-insert the criteria in the data to update
      isset($params->filter->field) ? $field = $params->filter->field : $field = "id";
      $data[$field] = $criteria;

      //Validate Request
      if (isset($this->model->requestValidation['update'])) {
        $this->validateRequestApi(new $this->model->requestValidation['update']($modelData));
      }

      //Update model
      $model = $this->modelRepository->updateBy($criteria, $modelData, $params);

      //Create or Update Settings
      if (isset($modelData["settings"]) &&
        (isset($params->permissions["profile.settings.edit"]) && $params->permissions["profile.settings.edit"])
      ) {
        foreach ($modelData["settings"] as $settingName => $setting) {
          Setting::updateOrCreate(
            ['related_id' => $model->id, 'entity_name' => 'role', 'name' => $settingName],
            ['related_id' => $model->id, 'entity_name' => 'role', 'name' => $settingName, 'value' => $setting]
          );
        }
      }

      //Throw exception if no found item
      if (!$model) throw new Exception('Item not found', 204);

      //Response
      $response = ["data" => CrudResource::transformData($model)];
      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = $status == 409 ? json_decode($e->getMessage()) :
        ['messages' => [['message' => $e->getMessage(), 'type' => 'error']]];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }
}
