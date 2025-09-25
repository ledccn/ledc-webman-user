<?php

namespace Ledc\Layui;

/**
 * 模板全局配置
 */
class Config
{
    /**
     * 网站域名（不含协议）
     * @var string
     */
    public string $domain = '';
    /**
     * 网站名称
     * @var string
     */
    public string $title = '';
    /**
     * 关键字
     * @var string
     */
    public string $keywords = '';
    /**
     * 描述
     * @var string
     */
    public string $description = '';
    /**
     * 网站标题后缀
     * @var string
     */
    public string $title_suffix = '';
    /**
     * 网站作者
     * @var string
     */
    public string $author = '';
    /**
     * 网站图标
     * @var string
     */
    public string $favicon = '/favicon.ico';
    /**
     * 网站图标
     * @var string
     */
    public string $logo = '';
    /**
     * 网站版权
     * @var string
     */
    public string $copyright = '';
    /**
     * 网站ICP备案号
     * @var string
     */
    public string $icp = '';
    /**
     * 网站公安备案号
     * @var string
     */
    public string $police = '';
    /**
     * 公安备案链接
     * @var string
     */
    public string $police_link = '';
    /**
     * 网站版本
     * @var string
     */
    public string $version = '';
    /**
     * CDN
     * @var string
     */
    public string $cdn = '';
    /**
     * 自定义HTML代码
     * - 输出到 </head> 之前
     * @var string
     */
    public string $custom_head_html = '';
    /**
     * 自定义CSS代码
     * - 输出到 </head> 之前
     * @var string
     */
    public string $custom_head_css = '';
    /**
     * 自定义JS代码
     * - 输出到 </body> 之前
     * @var string
     */
    public string $custom_body_js = '';
    /**
     * 灰色模式
     * @var bool
     */
    public bool $grayscale = false;

    /**
     * 构造函数
     * @param array $config
     */
    final public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }

    /**
     * 创建实例
     * @return static
     */
    public static function make(): static
    {
        $config = config('layui_config') ?: [];
        return new static($config);
    }
}
