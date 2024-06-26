<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SuperUser;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    //

    public function createUser(Request $request)
    {
        $token = $request->header('Authorization');

        $superuser = SuperUser::where('api_token', substr($token, 7))->first();

        if (!$superuser) {
            return response()->json('Not authorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::factory()->create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => $request->password
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User creation failed: ' . $e->getMessage()], 500);
        }


        $api_key = $user->createToken('user', ['link:add', 'link:get', 'link:get_list'])->plainTextToken;

        $api_key = substr($api_key, 2);

        return response()->json(['api_key' => $api_key]);
    }

    public function createLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'link' => 'required|url',
            'public' => 'required|boolean',
            'short_token' => 'nullable|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $short_token = $request->short_token;

        if (!$request->short_token || empty($request->short_token)) {
            $short_token = Str::random(8);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['user' => 'The provided credentials are incorrect.'], 401);
        } else if (!Hash::check($request->password, $user->password)) {
            return response()->json(['password' => 'The provided credentials are incorrect.'], 401);
        }


        try {
            $res = $user->links()->create([
                'link' => $request->link,
                'short_token' => $short_token,
                'public' => $request->public,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Link creation failed: ' . $e->getMessage()], 500);
        }



        return response()->json(['response' => $res]);
    }

    public function getLinks(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (!$email && !$token) {
            $links = Link::with('user')->where('public', true)->get();

            return response()->json(['links' => $links]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json("The user not found", 404);
        }

        if ($email && !$token) {
            $links = $user->links()->get();

            return response()->json(['user-links' => $links]);
        }

        if ($email && $token) {
            $link = $user->links()->where('short_token', $token)->first();
            if (!$link) {
                return response()->json("The link was not found for the passed token", 404);
            }
            return response()->json(['user-link' => $link]);
        }


    }

    public function redirectToLink(Request $request, string $user, string $short_token)
    {
        $user = User::where('email', $user)->first();

        if (!$user) {
            return response()->json("The user not found", 404);
        }

        $link = $user->links()->where('short_token', $short_token)->first();

        if (!$link) {
            return response()->json("The link was not found for the passed token", 404);
        } else if (!$link->public) {
            return response()->json("The link is private", 403);
        }
        return redirect($link->link);
    }
}
