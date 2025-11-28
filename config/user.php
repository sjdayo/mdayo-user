<?php
return [
   'model'=>Mdayo\User\Models\User::class,
   'auth_token_name'=> env('AUTH_TOKEN_NAME','api-token'),
   'default_user_role' => env('DEFAULT_USER_ROLE','admin')
];