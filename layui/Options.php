<?php

namespace Ledc\Layui;

use JsonSerializable;

/**
 * 模板配置项
 */
class Options implements JsonSerializable
{
    /**
     * Header区域：keywords
     * @var string
     */
    public string $keywords = '';
    /**
     * Header区域：description
     * @var string
     */
    public string $description = '';
    /**
     * Header区域：css
     * @var array
     */
    public array $css = [];
    /**
     * Header区域：js
     * @var array
     */
    public array $js = [];
    /**
     * Footer区域：html
     * @var array
     */
    public array $html = [];
    /**
     * 菜单栏是否隐藏
     * @var bool
     */
    public bool $ws_menu_bar_hide = false;

    /**
     * 构造函数
     * @param array $attributes
     */
    final public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
