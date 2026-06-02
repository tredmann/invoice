<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.user-panel.index', [
            'users' => User::orderBy('name')->paginate(15),
        ]);
    }

    public function store(UserRequest $request)
    {
        $input = $request->validated();

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        return redirect(route('admin.user-panel.index'))->with('success', 'Erfolgreich Nutzer erstellt!');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.user-panel.create');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect(route('admin.user-panel.index'))->with('delete', 'Nutzer erfolgreich gelöscht!');
    }

    public function update(UserRequest $request, User $user)
    {
        $input = $request->validated();

        if ($request->filled('password')) {
            $input['password'] = Hash::make($request['password']);
        }

        $user->update($input);

        return redirect(route('admin.user-panel.index'))->with('success', 'Nutzer erfolgreich aktualisiert!');
    }

    public function demote(User $user)
    {
        if ($user->is_admin) {
            $user->is_admin = false;
            $user->save();

            return redirect(route('admin.user-panel.index'))->with(
                'success',
                'Admin erfolgreich zum Nutzer degradiert!',
            );
        }

        return redirect(route('admin.user-panel.index'))->with('error', 'Nutzer ist bereits Nutzer!');
    }

    public function promote(User $user)
    {
        if (! $user->is_admin) {
            $user->is_admin = true;
            $user->save();

            return redirect(route('admin.user-panel.index'))->with(
                'success',
                'Nutzer erfolgreich zum Admin befördert!',
            );
        }

        return redirect(route('admin.user-panel.index'))->with('error', 'Dieser Nutzer ist bereits Admin!');
    }

    public function edit(User $user): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.user-panel.edit', [
            'user' => $user,
        ]);
    }
}
