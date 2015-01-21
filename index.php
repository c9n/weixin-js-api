<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="address=no"/>
    <title>微信 JS-API Demo</title>
</head>
<body>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="http://cdn.staticfile.org/zepto/1.0rc1/zepto.min.js"></script>
<script>
    'use strict';

    <?php
        require_once 'WeixinJsApi.php';

        $weixin = new WeixinJsApi('', '');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $config = $weixin->get_config($url);
    ?>

    var config = <?php echo json_encode($config); ?>;

    config['debug'] = true;
    config['jsApiList'] = ['checkJsApi'];

    wx.config(config);
</script>
</body>
</html>
