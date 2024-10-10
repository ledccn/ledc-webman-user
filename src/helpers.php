<?php

namespace Ledc\WebmanUser;

use plugin\admin\app\model\Base;
use support\exception\BusinessException;
use support\Model;
use support\Redis;

/**
 * 当前登录用户id
 * @return integer|null
 */
function user_id(): ?int
{
    return session('user.id');
}

/**
 * 检查模型的只读字段，禁止变更
 * @param array $readonly
 * @param Model|Base $model
 * @return void
 * @throws BusinessException
 */
function model_verify_readonly_field(array $readonly, Model|Base $model): void
{
    $dirty = $model->getDirty();
    foreach ($readonly as $field) {
        if (array_key_exists($field, $dirty)) {
            throw new BusinessException("禁止修改只读字段{$field}");
        }
    }
}

/**
 * Redis的setNx指令，支持同时设置ttl
 * - 使用lua脚本实现
 * @param string $key 缓存的key
 * @param string $value 缓存的value
 * @param int $ttl 存活的ttl，单位秒
 * @return bool
 */
function redis_set_nx(string $key, string $value, int $ttl = 10): bool
{
    static $scriptSha = null;
    if (!$scriptSha) {
        $script = <<<luascript
local result = redis.call('SETNX', KEYS[1], ARGV[1]);
if result == 1 then
    return redis.call('expire', KEYS[1], ARGV[2])
else
    return 0
end
luascript;
        $scriptSha = Redis::script('load', $script);
    }
    return (bool)Redis::rawCommand('evalsha', $scriptSha, 1, $key, $value, $ttl);
}

/**
 * Redis的incr指令，支持设置ttl
 * - 使用lua脚本实现
 * @param string $key 缓存的key
 * @param int $ttl 存活的ttl，单位秒
 * @return int
 */
function redis_incr(string $key, int $ttl = 10): int
{
    static $scriptSha = null;
    if (!$scriptSha) {
        $script = <<<luascript
if redis.call('set', KEYS[1], ARGV[1], "EX", ARGV[2], "NX") then
    return ARGV[1]
else
    return redis.call('incr', KEYS[1])
end
luascript;
        $scriptSha = Redis::script('load', $script);
    }
    return (int)Redis::rawCommand('evalsha', $scriptSha, 1, $key, 1, $ttl);
}

/**
 * 用Redis限流
 * - 使用lua脚本实现
 * @param string $key 限制资源：KEY
 * @param int $limit 限制规则：次数
 * @param int $window_time 窗口时间，单位：秒
 * @return int
 */
function redis_rate_limiter(string $key, int $limit, int $window_time = 10): int
{
    static $scriptSha = null;
    if (!$scriptSha) {
        $script = <<<luascript
if redis.call('set', KEYS[1], 1, "EX", ARGV[2], "NX") then
    return 1
else
    if tonumber(redis.call("GET", KEYS[1])) >= tonumber(ARGV[1]) then
        return 0
    else
        return redis.call("INCR", KEYS[1])
    end
end
luascript;
        $scriptSha = Redis::script('load', $script);
    }
    return (int)Redis::rawCommand('evalsha', $scriptSha, 1, $key, $limit, $window_time);
}