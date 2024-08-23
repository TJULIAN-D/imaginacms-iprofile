<?php

namespace Modules\Iprofile\Transformers;

use Modules\Core\Icrud\Transformers\CrudResource;

class AddressTransformer extends CrudResource
{
  /**
  * Method to merge values with response
  *
  * @return array
  */
  public function modelAttributes($request)
  {
    return [
      'country' => $this->when($this->countryIso2, $this->countryIso2),
      'state_id' => $this->when($this->state_id, $this->state_id),
      'appSuit' => $this->when($this->app_suit, $this->app_suit)
    ];
  }
}
