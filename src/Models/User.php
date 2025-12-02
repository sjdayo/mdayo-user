<?php

namespace Mdayo\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Mdayo\User\Models\Traits\UserModelTrait;

class User extends Authenticatable
{
    use UserModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];
 

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    } 
    protected $guard_name = 'user';
}
