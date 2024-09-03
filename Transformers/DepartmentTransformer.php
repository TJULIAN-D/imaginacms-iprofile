<?php

namespace Modules\Iprofile\Transformers;

use Modules\Core\Icrud\Transformers\CrudResource;

class DepartmentTransformer extends CrudResource
{
  /**
  * Method to merge values with response
  *
  * @return array
  */
  public function modelAttributes($request)
  {
    return [];
  }
}
