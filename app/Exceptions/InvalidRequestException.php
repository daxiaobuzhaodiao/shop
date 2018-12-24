<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    // 这个自定义的错误类全部由我们自己手动调用 抛出
    // 用户错误行为发生的异常
    // 参数1 错误信息
    // 参数2 错误代码
    // 调用方法：throw new InvalidRequestException('出错了', 400);
    function __construct(string $message = "", int $code = 400, $previous = NULL)
    {
        parent::__construct($message, $code); // 重载父类的 构造函数
    }

    // 将异常渲染给制定视图
    public function render(Request $request)
    {
        // 判断如果请求是ajax的话
        if($request->expectsJson()) {
            return response()->json(['msg'=>$this->message], $this->code);
        };
        return view('pages.error', ['msg'=>$this->message]);
    }
}
