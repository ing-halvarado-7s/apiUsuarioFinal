<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Auth;
use App\Models\User;

class UserController extends Controller
{

//****************** INICIAR SESIÓN  *******************************

    public function iniciarsesion(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'code' => 400,
                    'message' => 'Correo o clave no válidos.'
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }//fin de iciarSesion


    //******************* VALIDAR TOKEN ***************************** */
    // A cada función que necesitemos que solo sea usado por usuarios validados se
    // debe agregar esta función antes de escribir el código propio de la function

    public function validarToken()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
    }//fin de validarToker


    //******************* CERRAR SESIÓN ***************************** */

    public function cerrarSesion(Request  $request)
    {
        $this->validarToken();

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Cierre de sesión exitoso.'
            ], 200);
        } catch (JWTException  $exception) {
            return response()->json([
                'status' => false,
                'code' => 406,
                'message' => 'No se pudo cerrar la sesión, intente nuevamente.'
            ], 406);
        }
    }//fin de cerrar sesión


    // ************************** CREAR USUARIO **************************
    public function crear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'edad' => 'required|integer|max:100',
            'fecha_nacimiento' =>'required' ,
            'sexo' => 'required|string|max:1|in:F,M',
            'dni' => 'required|string|max:10|unique:users',
            'direccion' => 'required|string|max:250',
            'pais' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'errors' =>  $validator->messages(),
            ], 400);
        }

        $datosUsuario = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'edad' => $request->get('edad'),
            'fecha_nacimiento' => $request->get('fecha_nacimiento'),
            'sexo' => $request->get('sexo'),
            'dni' => $request->get('dni'),
            'direccion' => $request->get('direccion'),
            'pais' => $request->get('pais'),
            'telefono' => $request->get('telefono')
        ]);

        $token = JWTAuth::fromUser($datosUsuario);

        return response()->json([
            'status' => true,
            'code' => 201,
            'data' => $datosUsuario,
            'token' => $token
        ], 201);
    }//fin de crear

    //***************** MOSTRAR LISTADO DE USUARIOS *************/
    public function listar()
    {
        $this->validarToken();
        
        $datosUsuarios = User::all();

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $datosUsuarios,
        ], 200);
    }//fin de listar

    //*************************ACTUALIZAR USUARIO *************** */

    public function actualizar(Request $request, $id)
    {
        $this->validarToken();

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => 'string|min:6|confirmed',
            'edad' => 'integer|max:100',
            'sexo' => 'string|max:1|in:F,M',
            'dni' => 'string|max:10|unique:users',
            'direccion' => 'string|max:250',
            'pais' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'errors' =>  $validator->messages(),
            ], 400);
        }

        $datosUsuario = User::findOrFail($id);
        $datosUsuario->fill($request->all());
        $datosUsuario->save();

        $token = JWTAuth::fromUser($datosUsuario);

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $datosUsuario,
            'token' => $token
        ], 200);
    }//fin de actualizar



    //************************* MOSTRAR DETALLE DE USUARIO *************** */

    public function mostrarDetalle($id)
    {
        $this->validarToken();
            
        $datosUsuario = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $datosUsuario,
        ], 200);
    }// fin de mostrarDetalle


    //************************* BORRAR USUARIO *************** */

    public function eliminar($id)
    {
        $this->validarToken();

        $datosUsuario = User::findOrFail($id);
        $datosUsuario->delete();

        return response()->json([
            'status' => true,
            'code' => 204,
            'message' => 'Usuario eliminado.',
        ], 204);
    }//fin de eliminar
}
