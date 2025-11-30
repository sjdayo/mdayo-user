<?php
use Illuminate\Support\Facades\Hash;
return [
   'model'=>Mdayo\User\Models\User::class,
   'auth_token_name'=> env('AUTH_TOKEN_NAME','api-token'),
   'default_user_role' => env('DEFAULT_USER_ROLE','admin'),
   'default_admin'=>[
        'email' => 'admin@gmail.com',
        'name'=> 'admin',
        'password'=>Hash::make('123456')
   ]
];