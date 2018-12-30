<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\CouponCodeUnavailableException;

class Handler extends ExceptionHandler
{
    // 当一个异常被触发时，Laravel 会去检查这个异常的类型是否在 $dontReport 属性中定义了，如果有则不会打印到日志文件中
    protected $dontReport = [
        // 用户错误行为的异常不写入日志
        InvalidRequestException::class,
        CouponCodeUnavailableException::class
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
        return parent::render($request, $exception);
    }
}
