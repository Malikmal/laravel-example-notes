<?php

namespace App\Exceptions;

use App\Utils\ResponseJson;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if($request->is('api/*')) // exclue validation exception because has own response handler
            return ResponseJson::failed(
                data: app()->isProduction() ? [] : collect($e->getTrace())->take(5),
                message: app()->isProduction() ? __('response.error') : $e->getMessage(),
                code: $response?->getStatusCode() ?: 500, //getStatusCode for instanceof HttpException let keep original statuscode otherwise is 500
            );
    }



    /**
     * @override Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return ResponseJson::failed(
            data: $exception->errors(),
            message: $exception->getMessage(),
            code: $exception->status
        );
    }

}
