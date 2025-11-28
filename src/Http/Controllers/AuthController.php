<?php

namespace Mdayo\User\Http\Controllers;

use Mdayo\User\Http\Requests\AuthRegisterRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mdayo\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * @group User Management
 * APIs for user registration, and authentication
 */

/**
 * @OA\Tag(
 *     name="User",
 *     description="Operations for User registration and authentication"
 * )
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id","name","email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-28T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-28T10:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="UserBearerToken",
 *     type="object",
 *     title="User with Token",
 *     @OA\Property(property="access_token", type="string", example="1|abc123xyz"),
 *     @OA\Property(property="token_type", type="string", example="Bearer"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin"}),
 *     @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"edit articles","manage users"}),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *     schema="UserInfo",
 *     type="object",
 *     title="User Info",
 *     @OA\Property(property="info", ref="#/components/schemas/User"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin"}),
 *     @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"edit articles","manage users"})
 * )
 */
class AuthController extends Controller
{
    /**
     * Standard success response
     */
    private function successResponse(string $message, $data = null, int $status = 200)
    {
        return response()->json([
            'code' => 0,
            'success' => true,
            'error' => null,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * @OA\Post(
     *     path="/user/register",
     *     tags={"User"},
     *     summary="Register a new user",
     *     description="Creates a new user with name, email, and password. Returns the user object.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secretpassword"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secretpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="User registration successful"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */
    public function register(AuthRegisterRequest $request)
    {
        $validated = $request->validated();
        extract($validated);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);
        $userRole = config('user.default_user_role','admin');
        if($request->user())
        {
            $userRole = $request->role;   
        }
        $role = Role::firstOrCreate(['name' => $userRole]);
        $user->assignRole($role);

        return $this->successResponse('User registration successful', $user);
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     tags={"User"},
     *     summary="User login",
     *     description="Logs in a user and returns an access token along with roles and permissions.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secretpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(property="data", ref="#/components/schemas/UserBearerToken")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken(config('user.auth_token_name', 'api-token'))->plainTextToken;

        return $this->successResponse('Login successful.', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'user' => $user
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user/info",
     *     tags={"User"},
     *     summary="Get logged-in user info",
     *     description="Returns the currently authenticated user's info, roles, and permissions.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User info retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Get info successful"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserInfo")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $data = [
            'info' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ];

        return $this->successResponse('Get info successful', $data);
    }

    /**
     * @OA\Post(
     *     path="/user/logout",
     *     tags={"User"},
     *     summary="User logout",
     *     description="Logs out the user and revokes their token.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Log out successful")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse('Log out successful');
    }
}
