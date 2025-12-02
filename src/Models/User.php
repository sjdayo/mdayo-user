<?php

namespace Mdayo\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Mdayo\User\Models\UserModelTrait;

class User extends Authenticatable
{
    use UserModelTrait;
}
