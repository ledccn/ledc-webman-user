<?php

namespace Ledc\WebmanUser;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 跨域中间件
 */
class AccessControlMiddleware implements MiddlewareInterface
{
    /**
     * @param Request|\support\Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request|\support\Request $request, callable $handler): Response
    {
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        $response->withHeaders($this->getAllowHeaders($request));

        return $response;
    }

    /**
     * 获取允许的http头
     * @param Request|\support\Request $request
     * @return array
     */
    protected function getAllowHeaders(Request|\support\Request $request): array
    {
        return [
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ];
    }
}
