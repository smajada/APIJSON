<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
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

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {

//        $errors = [];
//        foreach ($exception->errors() as $field => $message) {
//            $pointer = '/' . str_replace('.', '/', $field);
//
//            $errors[] = [
//                'title' => $title,
//                'detail' => $message[0],
//                'source' => [
//                    'pointer' => $pointer
//                ]
//            ];
//        };


        $title = $exception->getMessage();

        return response()->json([
            'errors' => collect($exception->errors())
                ->map(function ($messages, $field) use ($title) {
                    return [
                        'title' => $title,
                        'detail' => $messages[0],
                        'source' => [
                            'pointer' => "/" . str_replace('.', '/', $field)
                        ]
                    ];
                })->values()
        ], 422, [
            'Content-Type' => 'application/vnd.api+json'
        ]);
    }


}
