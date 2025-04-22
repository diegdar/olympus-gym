<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{    
    public static function middleware(): array
    {
        return [            
            new Middleware('permission:admin.users.index', only: ['index']),
            new Middleware('permission:admin.users.edit', only: ['edit', 'update']),
            new Middleware('permission:admin.users.destroy', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user):RedirectResponse
    {
        $user->roles()->sync($request->roles);
        
        return redirect()->route('admin.users.index', $user)->with('msg', "Se asignó los roles correctamente al usuario {$user->name}");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('msg', 'El usuario ha sido eliminado correctamente');
    }   

}
