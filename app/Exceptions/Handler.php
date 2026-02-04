<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            if ($exception instanceof ValidationException) {
                return new JsonResponse([
                    'message' => $exception->getMessage() ?: 'The given data was invalid.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof AuthenticationException) {
                return new JsonResponse([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            if ($exception instanceof AuthorizationException) {
                return new JsonResponse([
                    'message' => $exception->getMessage() ?: 'Forbidden.',
                ], 403);
            }

            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
                $headers = $exception->getHeaders();
                $message = $exception->getMessage();

                if (!$message) {
                    $message = $status === 404 ? 'Not Found.' : 'Request failed.';
                }

                return new JsonResponse([
                    'message' => $message,
                ], $status, $headers);
            }

            $debug = (bool) config('app.debug');
            return new JsonResponse($debug ? [
                'message' => $exception->getMessage() ?: 'Server Error',
                'exception' => get_class($exception),
            ] : [
                'message' => 'Server Error',
            ], 500);
        }
        return parent::render($request, $exception);
    }
}
