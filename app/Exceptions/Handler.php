<?php

namespace App\Exceptions;

use App\Utility\NgeniusUtility;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof Redirectingexception) {
            return redirect()->back();
        }

        if($this->isHttpException($e))
        {
            if ($request->is('customer-products/admin')) {
                return NgeniusUtility::initPayment();
            }
            
            return parent::render($request, $e);
        }
        else
        {
            return parent::render($request, $e);
        }
    }
}