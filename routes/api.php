<?php

use App\Models\User;
use App\Models\SuperUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::post('/user', function (Request $request) {
    $user = SuperUser::where('email', $request->email)->first();
    return response()->json($user->tokens);
})->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        'token_name' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return ([
            'password' => ['The provided credentials are incorrect.'],
        ]);
    }
    $text = $user->createToken($request->token_name)->plainTextToken;

    return response($text);
});

Route::get('/text', function (): Response {
    return response("answer from the text route(Changedfwefwe)");
});

// Up5o3dgxAEMgyO3CJSEQN0Sscak3xmqIw0bymOua5d303552

// 1|Up5o3dgxAEMgyO3CJSEQN0Sscak3xmqIw0bymOua5d303552