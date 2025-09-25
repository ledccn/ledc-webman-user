<?php

namespace Ledc\Layui;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * 导航
 */
class Navs
{
    /**
     * 获取导航回调
     */
    private static ?Closure $fn = null;

    /**
     * 设置导航回调
     * @param Closure $fn
     */
    public static function setNavs(Closure $fn): void
    {
        self::$fn = $fn;
    }

    /**
     * 获取导航
     * @return Collection|SupportCollection
     */
    public static function navs(): Collection|SupportCollection
    {
        if (!self::$fn) {
            return new Collection([]);
        }
        return call_user_func(self::$fn);
    }
}
