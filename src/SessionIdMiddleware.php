<?php

namespace Ledc\WebmanUser;

use Exception;
use support\Request as SupportRequest;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * Token转换Session
 * - 从请求头获取token值，设置session_id.
 */
class SessionIdMiddleware implements MiddlewareInterface
{
    /**
     * @param Request|SupportRequest $request
     * @param callable $handler
     * @return Response
     * @throws Exception
     */
    public function process(Request|SupportRequest $request, callable $handler): Response
    {
        $token = $request->header('token', $request->cookie('token'));
        if ($token && ctype_alnum($token) && strlen($token) <= 70) {
            $request->sessionId($token);
        }

        return $handler($request);
    }
}
