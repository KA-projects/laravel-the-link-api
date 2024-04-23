<?php

use App\Models\User;
use App\Models\SuperUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::post('/user', function (Request $request) {
    $user = SuperUser::where('email', $request->email)->first();
    return response()->json($user->tokens);
})->middleware('auth:sanctum');

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

    return response()->json($api_key);
})->middleware('auth:sanctum');


Route::get('/text', function (): Response {
    return response("answer from the text route(Changedfwefwe)");
});
