<?php

namespace App\Http\Controllers\ApiTokenManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiTokenRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenManagerController extends Controller
{
    public function index()
    {
        return view('api-token-manager.index', [
            'apiTokens' => Auth::user()->tokens,
        ]);
    }

    public function store(ApiTokenRequest $request)
    {
        $input = $request->validated();

        $token = Auth::user()->createToken($input['name'], $input['permissions']);

        return redirect()->route('api-tokens.index')->with('plainTextToken', __('api-token-manager.api_token_copy') . $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $personalAccessToken)
    {
        Auth::user()->tokens()->where('id', $personalAccessToken->id)->delete();

        return redirect()->route('api-tokens.index')->with('success', __('api-token-manager.delete_success'));
    }

    public function updateForm(PersonalAccessToken $personalAccessToken)
    {
        return view('api-token-manager.update-form', [
            'apiToken' => $personalAccessToken,
        ]);
    }

    public function update(PersonalAccessToken $personalAccessToken, ApiTokenRequest $request)
    {
        $input = $request->validated();

        Auth::user()->tokens()->where('id', $personalAccessToken->id)->update([
            'abilities' => $input['permissions']]);

        return redirect()->route('api-tokens.index')->with('success', __('api-token-manager.update_success'));
    }
}
