<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof RoleDoesNotExist) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => $e->getMessage()
            ], 401);
        }   
        if ($e instanceof UnauthorizedException) {
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'El usuario no tiene los permisos adecuados.'
            ], 401);
        }
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
                'code' => 403,
                'message' => 'Prohibido. El invocador no está autorizado a invocar la operación.'
            ], 403);
        }
        if ($e instanceof ModelNotFoundException) {
            $message = ($e->getModel() === "App\Models\User") ? 'Usuario no encontrado' : $e->getMessage();

            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => $message
            ], 404);
        }
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'El objeto al que hace referencia la ruta no existe'
            ], 404);
        }
        if ($e instanceof RoleDoesNotExist) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => $e->getMessage()
            ], 401);
        }   
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => false,
                'code' => 405,
                'message' => $e->getMessage()
            ], 405);
        }
        if ($e instanceof JWTException) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage()
            ], 401);
        }

        return response()->json([
            'status' => false,
            'code' => 500,
            'message' => $e->getMessage()
        ], 500);

        return parent::render($request, $e);
    }
}
