<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    //******************** Validar solicitud de rol */
    private function validarRolRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuarioId' => 'required',
            'nombreRol' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationFails($validator);
        }
    }

    //******************** Validar solicitud de permiso */
    private function validatePermisoRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreRol' => 'required',
            'nombrePermiso' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationFails($validator);
        }
    }

    //******************** Responder en caso que las validaciones fallen */
    private function responseValidationFails($validator)
    {
        return response()->json([
            'status' => false,
            'code' => 400,
            'errors' =>  $validator->messages(),
        ], 400);
    }

    //******************** Responder con permiso no encontrado */
    private function responsePermisoNotFound()
    {
        return response()->json([
            'status' => false,
            'code' => 404,
            'errors' => 'Permiso no encontrado',
        ], 404);
    }
    

    //******************** ASIGNAR ROL AL USUARIO */
    public function asignarRol(Request $request)
    {
        if ($responseError = $this->validarRolRequest($request)) {
            return $responseError;
        }

        $datosUsuario = User::findOrFail($request->usuarioId);
        $datosUsuario->assignRole($request->nombreRol);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Se ha asignado el rol correctamente.',
        ], 200);
    }

    //******************** QUITAR ROL AL USUARIO */
    public function quitarRol(Request $request)
    {
        if ($responseError = $this->validarRolRequest($request)) {
            return $responseError;
        }

        $datosUsuario = User::findOrFail($request->usuarioId);
        $datosUsuario->removeRole($request->nombreRol);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Se ha removido el rol correctamente.',
        ], 200);
    }

    //******************** ASIGNAR PERMISO A ROL */
    public function agregarPermisoRol(Request $request)
    {
        if ($responseError = $this->validatePermisoRequest($request)) {
            return $responseError;
        }

        $permiso = Permission::where('name', '=', $request->nombrePermiso)->firstOrFail();
        $permiso->assignRole($request->nombreRol);

        return response()->json([
            'status' => true,
            'code' => 201,
            'data' => [
                'permiso' => $permiso
            ],
        ], 201);
    }


    //******************** REVOCAR PERMISO A ROL */
    public function quitarPermisoRol(Request $request)
    {
        if ($responseError = $this->validatePermisoRequest($request)) {
            return $responseError;
        }

        $rol= Role::where('name', $request->nombreRol)->firstOrFail();
        $permiso = Permission::where('name', '=', $request->nombrePermiso)->firstOrFail();
        $rol->revokePermissionTo($request->nombrePermiso);
        
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => [
                'permiso' => $permiso
            ],
        ], 200);
    }
}
