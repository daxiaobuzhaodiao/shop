<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

class InternalException extends Exception
{
    // 参数1 原本的异常信息
    // 参数2 告知用户的错误信息
    // 参数3 错误码
    protected $msgForUser;
    public function __construct(string $message, string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->msgForUser], $this->code);
        }

        return view('pages.error', ['msg' => $this->msgForUser]);
    }
}
