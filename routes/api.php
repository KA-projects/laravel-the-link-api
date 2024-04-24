<?php

use App\Models\User;
use App\Models\SuperUser;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/user/create', function (Request $request) {
    $token = $request->header('Authorization');

    $superuser = SuperUser::where('api_token', substr($token, 7))->first();

    if (!$superuser) {
        return response()->json('Not authorized');
    }

    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'name' => 'required',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::factory()->create([
        'email' => $request->email,
        'name' => $request->name,
        'password' => $request->password
    ]);

    $api_key = $user->createToken('user', ['link:add', 'link:get', 'link:get_list'])->plainTextToken;

    return response()->json(['api_key' => $api_key]);
})->middleware('auth:sanctum');

Route::post('/create-link', function (Request $request) {

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

    if (!$request->short_token || empty ($request->short_token)) {
        $short_token = Str::random(8);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return ([
            'password' => ['The provided credentials are incorrect.'],
        ]);
    }

    $res = $user->links()->create([
        'link' => $request->link,
        'short_token' => $short_token,
        'public' => $request->public,
    ]);

    return response()->json(['response' => $res]);
})->middleware('auth:sanctum');

Route::get('/get-links', function (Request $request) {
    $email = $request->query('email');
    $token = $request->query('token');

    if (!$email && !$token) {
        $links = Link::with('user')->where('public', true)->get();

        return response()->json(['links' => $links]);
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json("The user not found");
    }

    if ($email && !$token) {
        $links = $user->links()->get();

        return response()->json(['user-links' => $links]);
    }

    if ($email && $token) {
        $link = $user->links()->where('short_token', $token)->first();
        if (!$link) {
            return response()->json("The link was not found for the passed token");
        }
        return response()->json(['user-link' => $link]);
    }



})->middleware('auth:sanctum');
;


Route::get('/{user}/{short_token}', function (Request $request, string $user, string $short_token) {
    $user = User::where('email', $user)->first();

    if (!$user) {
        return response()->json("The user not found");
    }

    $link = $user->links()->where('short_token', $short_token)->first();

    if (!$link) {
        return response()->json("The link was not found for the passed token");
    } else if (!$link->public) {
        return response()->json("The link is private");
    }
    return redirect($link->link);
});


