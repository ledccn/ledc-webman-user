<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Ledc\Layui\Navs;
use Ledc\Layui\Template;

/**
 * 获取模板对象
 * - 模板使用，禁止改名
 * @return Template
 */
function layui_template(): Template
{
    return Template::make();
}

/**
 * 将特殊字符转换为HTML实体
 * - 模板使用，禁止改名
 * @param mixed $value
 * @param int $flags
 * @param string $encoding
 * @return string
 */
function html_special_chars(mixed $value, int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, string $encoding = 'UTF-8'): string
{
    $value = match (true) {
        is_string($value) => $value,
        is_int($value), is_float($value) => (string)$value,
        is_bool($value) => $value ? 'true' : 'false',
        is_null(null) => 'null',
        is_object($value) => match (true) {
            $value instanceof JsonSerializable => json_encode($value->jsonSerialize(), JSON_UNESCAPED_UNICODE),
            $value instanceof stdClass => json_encode($value, JSON_UNESCAPED_UNICODE),
            method_exists($value, '__toString') => $value->__toString(),
            method_exists($value, 'toJson') => $value->toJson(),
            method_exists($value, 'toArray') => json_encode($value->toArray(), JSON_UNESCAPED_UNICODE),
            default => (string)$value,
        },
        is_array($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
        default => throw new InvalidArgumentException('将特殊字符转换为HTML实体时异常：$value类型为：' . gettype($value)),
    };

    return htmlspecialchars($value, $flags, $encoding);
}

/**
 * 获取Layui导航栏数据
 * - 模板使用，禁止改名
 * @return Collection|SupportCollection
 */
function navs_layui(): Collection|SupportCollection
{
    return Navs::navs();
}

/**
 * 判断Nav导航的选中状态
 * - 模板使用，禁止改名
 * @param string $path
 * @return string
 */
function navs_layui_this(string $path): string
{
    $request = request();
    $haystack = trim($request ? $request->uri() : '', '/');
    $path = trim($path, '/');
    if (empty($haystack) && empty($path)) {
        return 'layui-this';
    }

    if (empty($path)) {
        return '';
    }

    $needle = str_contains($path, '/') ? explode('/', $path) : $path;
    if (is_array($needle)) {
        if (1 < count($needle)) {
            array_pop($needle);
        }
        $needle = implode('/', $needle);
    }
    return str_starts_with($haystack, $needle) ? 'layui-this' : '';
}
