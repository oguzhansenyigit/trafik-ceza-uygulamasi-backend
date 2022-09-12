<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    

    public function index() {

        
        $user = new User();

        if(request()->has('sort_by')) {
            $user = $user->orderBy(request()->sort_by, 'DESC');
        }
        
        $perPage = (request()->has('per_page'))?request()->per_page:env('PER_PAGE');
        return response()->json($user->paginate($perPage));
    }
    public function show(User $user)
    {
        return response()->json($user, 201);
    }
    public function update(Request $request, User $user)
    {

        $request->validate($rules = [
    
            'name' => 'required|max:150',
            'surname' => 'required|max:150',

        ]);

        $user->name = $request->name;
        $user->surname = $request->surname;

        if($user->isDirty()) {
            $user->save();
        }
        return response()->json(["message" => "Profil Ayrıntıları başarıyla güncellendi"], 201);
    }
}
