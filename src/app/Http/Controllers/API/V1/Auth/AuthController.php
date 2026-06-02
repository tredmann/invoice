<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $errors = [];
        foreach ($validator->errors()->getMessages() as $field => $value) {
            $errors['errors'][] = [
                'status' => 400,
                'source' => $field,
                'title' => __('api.title_invalid_attribute'),
                'detail' => $value,
            ];
        }

        if ($validator->fails()) {
            return response()->json($errors, 400);
        }

        $validatedData = $validator->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        $success = [
            'data' => [
                'type' => 'user',
                'attributes' => new UserResource($user->refresh()),
                'token' => $user->createToken($user->email)->plainTextToken,
            ],
        ];

        return response()->json($success, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $field => $value) {
                $errors['errors'][] = [
                    'status' => 400,
                    'source' => $field,
                    'title' => __('api.title_invalid_attribute'),
                    'detail' => $value,
                ];
            }

            return response()->json($errors, 400);
        }

        if (! Auth::attempt($validator->validated())) {
            $errors['errors'][] = [
                'status' => 401,
                'title' => __('api.title_auth_failed'),
                'detail' => __('auth.failed'),
            ];

            return response()->json($errors, 401);
        }

        $user = auth()->user();

        $success = [
            'data' => [
                'type' => 'user',
                'attributes' => new UserResource($user->refresh()),
                'token' => $user->createToken($user->email)->plainTextToken,
            ],
        ];

        return response()->json($success, 201);
    }

    public function logout(Request $request)
    {
        $token = PersonalAccessToken::where('id', '=', $request->bearerToken())->first();
        $token->delete();

        return response('', 204);
    }
}
