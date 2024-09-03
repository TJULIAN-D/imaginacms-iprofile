<?php

namespace Modules\Iprofile\Repositories\Eloquent;

use Modules\Iprofile\Repositories\RoleApiRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Modules\Iprofile\Entities\Setting;

class EloquentRoleApiRepository extends EloquentCrudRepository implements RoleApiRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected $replaceFilters = [];

  /**
   * Relation names to replace
   * @var array
   */
  protected $replaceSyncModelRelations = [];

  /**
   * Attribute to define default relations
   * all apply to index and show
   * index apply in the getItemsBy
   * show apply in the getItem
   * @var array
   */
  protected $with = ["all" => ['settings']];

  /**
   * Filter query
   *
   * @param $query
   * @param $filter
   * @param $params
   * @return mixed
   */
  public function filterQuery($query, $filter, $params)
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */
    /*=== SETTINGS ===*/
    if (isset($params->settings)) {
      $settings = $params->settings;
      if (isset($settings['assignedRoles']) && count($settings['assignedRoles'])) {
        $query->whereIn('id', $settings['assignedRoles']);
      }
    }

    //add filter by search
    if (isset($filter->search)) {
      //find search in columns
      $query->where(function ($query) use ($filter) {
        $query->where('id', 'like', '%' . $filter->search . '%')
          ->orWhere('name', 'like', '%' . $filter->search . '%')
          ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
          ->orWhere('created_at', 'like', '%' . $filter->search . '%');
      });
    }

    $this->defaultPreFilters($query, $params);

    //Response
    return $query;
  }

  /**
   * Method to sync Model Relations
   *
   * @param $model ,$data
   * @return $model
   */
  public function syncModelRelations($model, $data)
  {
    //Get model relations data from attribute of model
    $modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }

  private function defaultPreFilters($query, $params)
  {

    $entitiesWithCentralData = json_decode(setting("iprofile::tenantWithCentralData", null, "[]", true));
    $tenantWithCentralData = in_array("roles", $entitiesWithCentralData);

    if ($tenantWithCentralData && isset(tenant()->id)) {
      $model = $this->model;

      $query->withoutTenancy();
      $query->where(function ($query) use ($model) {
        $query->where($model->qualifyColumn(BelongsToTenant::$tenantIdColumn), tenant()->getTenantKey())
          ->orWhereNull($model->qualifyColumn(BelongsToTenant::$tenantIdColumn));
      });
    }

    if (isset($params->settings) && !empty($params->settings["assignedRoles"])) {
      $query->whereIn("roles.id", $params->settings["assignedRoles"]);
    }
  }
}
