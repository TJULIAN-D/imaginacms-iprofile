<?php

namespace Modules\Iprofile\Events;

use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
  use SerializesModels;
  public $user;
  public $bindings;

  public function __construct($user, $bindings)
  {
    \Log::info('Iprofile|Events|UserCreatedEvent');

    $this->user = $user;
    $this->bindings = $bindings;

  }
}
