<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    // 用户错误行为出发的异常
    function __construct(string $message = "", $code = 400, $previous = NULL)
    {
        parent::__construct($message, $code); // 重载父类的 构造函数
    }

    public function render(Request $request)
    {
        // 判断如果请求是ajax的话
        if($request->expectsJson()) {
            return response()->json(['msg'=>$this->message], $this->code);
        };
        return view('pages.error', ['msg'=>$this->message]);
    }
}
