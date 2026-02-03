<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\UserCreatedMail;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index()
    {
        return view('admins.index', [
            'admins' => User::role(['admin'])->paginate(10)
        ]);
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(AdminRequest $request)
    {
        $user = new User();

        $user
            ->fill($request->validated())
            ->fill(['enabled' => true, 'password' => str_random(60)])
            ->save();

        $user->assignRole('admin');

        Mail::to($user)->send(new UserCreatedMail(
            app('auth.password.broker')->createToken($user),
            $user,
            'emails.users.created',
            'Account created'
        ));

        return redirect()->route('admins.index')->with('success', 'Admin created');
    }

    public function edit(User $admin)
    {
        return view('admins.edit', [
            'admin' => $admin,
        ]);
    }

    public function update(AdminRequest $request, User $admin)
    {
        $admin->update($request->validated());

        return back()->with('success', 'Admin updated');
    }

    public function destroy(User $admin)
    {
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin deleted');
    }

    public function impersonate(User $user)
    {
        auth()->user()->setImpersonating($user->id);        
        
        return redirect()->route('index');
    }

    public function stopImpersonate()
    {
        auth()->user()->stopImpersonating();        
        
        return redirect()->route('index');
    }
}
