layui.define(['layer', 'util', 'jquery'], function (exports) {
    let layer = layui.layer, util = layui.util, jquery = layui.jquery;
    let bodyClass = null;

    console.log('定义模块')

    jquery('.ws-header-more').on("click", function() {
        jquery('#WS_BODY').addClass(bodyClass = "ws-nav-show ws-shade-show");
    });
    jquery('.ws-menu-bar').on("click", function() {
        jquery('#WS_BODY').addClass(bodyClass = "ws-menu-show ws-shade-show");
    });
    jquery('.ws-shade').on("click", function() {
        jquery('#WS_BODY').removeClass(bodyClass);
        bodyClass = null;
    });

    // 自定义固定条
    util.fixbar({
        bars: [{ // 定义可显示的 bar 列表信息 -- v2.8.0 新增
            type: 'share',
            icon: 'layui-icon-share'
        }, {
            type: 'help',
            icon: 'layui-icon-help'
        }, {
            type: 'cart',
            icon: 'layui-icon-cart',
            style: 'background-color: #FF5722;'
        }, {
            type: 'groups',
            content: '群',
            style: 'font-size: 21px;'
        }],
        on: {
            // 任意事件 --  v2.8.0 新增
            mouseenter: function (type) {
                layer.tips(type, this, {
                    tips: 4,
                    fixed: true
                });
            },
            mouseleave: function (type) {
                layer.closeAll('tips');
            }
        },
        // 点击事件
        click: function (type) {
            console.log(this, type);
        }
    });

    exports('index', {});
});