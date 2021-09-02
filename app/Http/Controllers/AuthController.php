<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;


class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $resp = new \stdClass();

        if ($validator->fails()) {
            $resp->error = $validator->errors();
            $resp->message = 'Campos con errores.';
            $resp->state = false;
            return response()->json($resp, 200);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            $resp->message = 'Acceso no autorizado.';
            $resp->state = false;
            return response()->json($resp, 200);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $user->roles()->attach(Role::where('name', 'user')->first());

        $resp = new \stdClass();
        $resp->message = 'Usuario exitosamente registrado';
        $resp->user = $user;
        $resp->state = true;
        return response()->json($resp, 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Se ha cerrado exitosamente la sesión'], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Perfil de usuario obtenido';
        $resp->user = auth()->user();
        return response()->json($resp, 200);
    }

    public function updateAddress(Request $request){
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|between:2,100',
            'city' => 'required|string|between:2,100',
            'region' => 'nullable|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $user = User::find(auth()->user()->id);

        if ($user->address != $request->address) {
            $user->address = $request->address;
        }

        if ($user->region != $request->region) {
            $user->region = $request->region;
        }

        if ($user->city != $request->city) {
            $user->city = $request->city;
        }

        $user->save();
        $user = User::find(auth()->user()->id);

        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Dirección de usuario actualizado';
        $resp->user = User::find(auth()->user()->id);
        return response()->json($resp, 200);
    }

    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'telephone' => 'nullable|integer|regex:/^([56]\d[2-9]\d{8})$/i',
            'birthday' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $user = User::find(auth()->user()->id);

        if($user->name != $request->name){
            $user->name = $request->name;
        }

        if (isset($request->telephone) && $user->phone != $request->telephone) {
            $user->phone = $request->telephone;
        }

        $user->save();
        $user = User::find(auth()->user()->id);

        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Perfil de usuario actualizado';
        $resp->user = User::find(auth()->user()->id);
        return response()->json($resp, 200);
    }

    public function changePhotoProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'img_profile' => 'required|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'No cumple con las precondiciones de los campos';
            $resp->errors = $validator->errors();
            return response()->json($resp, 200);
        }

        $user = User::find(auth()->user()->id);
        $path = $request->img_profile->store('public/profile');
        $path = str_replace("public/", "storage/", $path);
        $user->img_profile = $path;
        $user->save();
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Perfil de usuario obtenido';
        $resp->user = User::find(auth()->user()->id);
        return response()->json($resp, 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Inicio de sesión exitoso';
        $resp->user = User::with('roles')->find(auth()->user()->id);
        $resp->token = $token;
        $resp->expire_in = auth()->factory()->getTTL() * 60;
        return response()->json($resp, 200);
    }

}
