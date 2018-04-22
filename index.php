<?php
    if(!file_exists('vendor/autoload.php')) {
        die('Composer autoload file not found! See installation section in README.md file.');
    }
    require 'vendor/autoload.php';
    $new_secret_key = substr(md5(rand(0, 100000)), 0, 10);
    $config = Spyc::YAMLLoad('config.yml');
    $node_address = "$config[node_protocol]://$config[node_address]:$config[node_port]";
    if (isset($_GET['secret_key']))
        $secret_key = $_GET['secret_key'];
    else
        $secret_key = $new_secret_key;
    if (count($_POST) != 0) {
        function getKintHtmlBlock($type = 'another'){
            ob_start(); 
            Kint::$display_called_from = false;
            switch ($type) {
                case '$_POST':
                    Kint::dump($_POST); 
                    break;
                case '$_GET':
                    Kint::dump($_GET); 
                    break;
            }
            $dump = ob_get_clean();
            return ($dump);
        }
        $json_post_block = json_encode(getKintHtmlBlock('$_POST'));
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL, $node_address.'/?page='.$_GET['page'].'&secret_key='.$_GET['secret_key'].'&client_ip='.$_SERVER['REMOTE_ADDR']);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('POST' => $json_post_block)));
        $curl_status = curl_exec($ch);
        curl_close($ch);
        exit(($curl_status) ? 'success' : 'failure');
    }
?>
<html>
	<head>
		<link rel="icon" type="image/png" href="/favicon.png" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<title>RequestObserver : просмотр запросов</title>
		<script type="text/javascript" src="node_modules/dtf/dtf.min.js"></script>
        <script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
        <?if (isset($_GET['secret_key'])):?>
            <script src="node_modules/socket.io-client/dist/socket.io.js"></script>
            <script defer type="text/javascript" src="assets/requestobserver.js"></script>
            <script type="text/javascript">
                var socket_address = '<?=$node_address?>';
            </script>
        <?endif;?>
        <link rel="stylesheet" href="assets/requestobserver.css"/>
        <script type="text/javascript">
			!function(){"use strict";function e(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,n)}function t(e){return window.localStorage&&localStorage.font_css_cache&&localStorage.font_css_cache_file===e}function n(){if(window.localStorage&&window.XMLHttpRequest)if(t(o))c(localStorage.font_css_cache);else{var n=new XMLHttpRequest;n.open("GET",o,!0),e(n,"load",function(){4===n.readyState&&(c(n.responseText),localStorage.font_css_cache=n.responseText,localStorage.font_css_cache_file=o)}),n.send()}else{var a=document.createElement("link");a.href=o,a.rel="stylesheet",a.type="text/css",document.getElementsByTagName("head")[0].appendChild(a),document.cookie="font_css_cache"}}function c(e){var t=document.createElement("style");t.innerHTML=e,document.getElementsByTagName("head")[0].appendChild(t)}var o="/font.css";window.localStorage&&localStorage.font_css_cache||document.cookie.indexOf("font_css_cache")>-1?n():e(window,"load",n)}();
        </script>
	</head>
	<body>
        <div class="wrapper">
            <ul class="nav nav-tabs">
                <? if (isset($_GET['secret_key'])): ?>
                <li role="presentation" class="active"><a data-toggle="tab" href="#requestlist">Список запросов <span class="badge" id="requestCounter">0</span></a></li>
                <? endif; ?>
                <li role="presentation" <? if (!isset($_GET['secret_key'])) echo 'class="active"'?>><a data-toggle="tab" href="#manual">Инструкция</a></li>
                <? if (!isset($_GET['secret_key'])): ?>
                <a class="btn btn-info pull-right" href="?secret_key=<?=$new_secret_key?>">Начать работу</a>
                <? endif; ?>
            </ul>
            <div class="tab-content">
                <? if (isset($_GET['secret_key'])): ?>
                <div id="requestlist" class="tab-pane fade in active">
                    <div class="panel panel-default" style="height: calc(100% - 40px); margin: 0;">
                        <div class="panel-heading" style="height: 50px;">
                            <a class="btn btn-warning btn-sm pull-left" href="?secret_key=<?=$new_secret_key?>" id="changeSecret">
                                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Сменить ключ
                            </a>
                            <button type="button" title="Копировать ключ в буфер обмена" class="btn btn-sm btn-default pull-left" style="margin-left: 5px;" id="copySecret">
                                <span class="glyphicon glyphicon-copy" aria-hidden="true"></span> <?=$secret_key;?> <span class="badge" id="userCounter" title="Количество пользователей" >0</span>
                            </button>
                            <button class="btn btn-danger btn-sm pull-left" style="margin-left: 5px;" id="requestCleanButton" disabled>
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> Очистить список
                            </button>
                            <!--<div class="btn-group btn-group-sm pull-right" role="group">
                                <button class="btn btn-info">POST</a>
                                <button class="btn btn-default disabled" disabled>GET</a>
                                <button class="btn btn-default disabled" disabled>POST+GET</a>
                            </div>-->
                        </div>
                        <div class="clearfix"></div>
                        <div class="panel-body" id="requestContainer" style="height: calc(100% - 50px); overflow-y: scroll;"></div>
                    </div>
                </div>
                <? endif; ?>
                <div id="manual" class="tab-pane fade <? if (!isset($_GET['secret_key'])) echo 'in active'?>">
                    <div class="panel panel-default" style="height: calc(100% - 40px); margin: 0;">
                        <div class="panel-body" style="height: 100%; overflow-y: scroll;">
                            <? include('templates/instruction.php') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</body>
</html>
<!-- Bootstrap -->
<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Bootstrap End -->