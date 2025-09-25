<?php

namespace Ledc\Layui;

use plugin\user\app\service\Register;
use stdClass;
use support\exception\BusinessException;
use Throwable;
use Webman\Event\Event;

/**
 * 原生渲染模板
 */
readonly class Template
{
    /**
     * 构造函数
     * @param Config $config 模板全局配置
     */
    final public function __construct(public Config $config)
    {
    }

    /**
     * 模板头部
     * @param string $title 标题
     * @param array|Options $options 模板参数
     * @return string
     */
    public function header(string $title = '', array|Options $options = []): string
    {
        if (is_array($options)) {
            $options = new Options($options);
        }

        $config = $this->config;
        // 基础配置
        $title = $title ?: $this->config->title;
        $favicon = $this->config->favicon;
        $keywords = $options->keywords ?: $this->config->keywords;
        $description = $options->description ?: $this->config->description;
        $title_suffix = $this->config->title_suffix;
        $grayscale = $this->config->grayscale;
        $version = $this->config->version;
        $cdn = $this->config->cdn;

        // html、css、js
        $custom_head_html = $this->config->custom_head_html;
        $custom_head_css = $this->config->custom_head_css;
        $css = $options->css;
        $js = $options->js;

        ob_start();
        include self::templatePath('header.html');
        return ob_get_clean() ?: '';
    }

    /**
     * 网页footer
     * @param array|Options $options
     * @return string
     */
    public function footer(array|Options $options = []): string
    {
        if (is_array($options)) {
            $options = new Options($options);
        }

        $version = $this->config->version;
        $custom_body_js = $this->config->custom_body_js;
        $ws_menu_bar_hide = $options->ws_menu_bar_hide;

        $icp = $this->config->icp;
        $police = $this->config->police;
        $police_link = $this->config->police_link;
        ob_start();
        include self::templatePath('footer.html');
        return ob_get_clean() ?: '';
    }

    /**
     * 模板导航
     * @return string
     */
    public function nav(): string
    {
        $navs = $this->getNavData()['navs'];
        if (!class_exists(Register::class)) {
            throw new BusinessException('请先安装webman用户模块');
        }
        $setting = Register::getSetting();
        ob_start();
        include self::templatePath('nav.html');
        return ob_get_clean() ?: '';
    }

    /**
     * 侧边栏sidebar
     * @return string
     * @throws Throwable
     */
    public function sidebar(): string
    {
        $sidebars = $this->getSidebarData()['sidebars'];
        ob_start();
        include self::templatePath('sidebar.html');
        return ob_get_clean() ?: '';
    }

    /**
     * 获取导航数据
     * @return array[]
     */
    public function getNavData(): array
    {
        $object = new stdClass();
        $object->navs = [];
        Event::emit('user.nav.render', $object);
        $navs = $object->navs;
        return [
            'navs' => $navs
        ];
    }

    /**
     * 获取侧边栏数据
     * @return array[]
     */
    public function getSidebarData(): array
    {
        $request = request();
        $uri = rtrim($request ? $request->uri() : '', '/');
        $object = new stdClass();
        $object->sidebars = [
            [
                'name' => '用户中心',
                'items' => [
                    ['name' => '个人资料', 'url' => '/app/user', 'class' => $uri == '/app/user' ? 'active' : ''],
                    ['name' => '头像设置', 'url' => '/app/user/avatar', 'class' => $uri == '/app/user/avatar' ? 'active' : ''],
                    ['name' => '密码设置', 'url' => '/app/user/password', 'class' => $uri == '/app/user/password' ? 'active' : ''],
                ]
            ]
        ];
        Event::emit('user.sidebar.render', $object);
        $sidebars = $object->sidebars;
        return [
            'sidebars' => $sidebars
        ];
    }

    /**
     * 模板路径
     * @param string $filename
     * @return string
     */
    final public static function templatePath(string $filename): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'Templates', ltrim($filename, '/\\')]);
    }

    /**
     * 当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用
     * @param int|string $name
     * @return bool
     */
    public function __isset(int|string $name): bool
    {
        $request = request();
        if (!$request) {
            return false;
        }

        $_view_vars = $request->_view_vars;
        if (empty($_view_vars)) {
            return false;
        }

        $vars = (array)$_view_vars;
        return isset($vars[$name]);
    }

    /**
     * 当访问不可访问属性时调用
     * @param int|string $name
     * @return array|string|null
     */
    public function __get(int|string $name)
    {
        return $this->get($name, '');
    }

    /**
     * 获取配置项参数
     * - 支持 . 分割符
     * @param int|string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    final public function get(int|string|null $key = null, mixed $default = null): mixed
    {
        $request = request();
        if (!$request) {
            return $default;
        }

        $_view_vars = $request->_view_vars;
        if (empty($_view_vars)) {
            return $default;
        }

        if (null === $key) {
            return $_view_vars;
        }
        $keys = explode('.', $key);
        $value = (array)$_view_vars;
        foreach ($keys as $index) {
            if (!array_key_exists($index, $value)) {
                return $default;
            }
            $value = $value[$index];
        }
        return $value;
    }

    /**
     * 创建实例
     * @return static
     */
    final public static function make(): static
    {
        $request = request();
        if (!$request) {
            return new static(Config::make());
        }
        $layui_template = $request->layui_template;
        if (!$layui_template) {
            $request->layui_template = $layui_template = new static(Config::make());
        }

        return $layui_template;
    }
}
