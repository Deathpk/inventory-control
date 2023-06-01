<?php

namespace App\Exceptions;

use App\Exceptions\Interfaces\CustomException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
 * Register the exception handling callbacks for the application.
 *
 * @return void
 */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->resolveApiException($e);
            }
        });
    }

    private function resolveApiException(Throwable &$e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $e instanceof CustomException ? $e->getStatusCode() : 500);
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
