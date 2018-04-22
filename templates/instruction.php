<h3>Использование</h3>
<div>
  <p>Для отслеживания запросов, открывайте данную страницу с параметром 
  и <span class="label label-default">?secret_key=[ваш_секретный_ключ]</span>
  из скрипта, запросы на который необходимо отслеживать.</p>
  <p><a href="#secret_more">Подробнее о секретном ключе</a>.</p>
</div>
<h3>PHP код <small>на сервере</small></h3>
<div>
  Для отслеживания запросов, добавьте в ваш код функцию, наподобие примера ниже, заменив переменную
  <span class="label label-default">$secret_key</span> на ваш текущий секретный ключ.
</div>
<pre class="php" style="margin: 10px;">
  function sendToObserver($data) {
    $requestUrl = '<?="http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>';
    $secret_key = '[ваш_секретный_ключ]';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl."?page=http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]".'&secret_key='.$secret_key);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array($data)));
    $curl_status = curl_exec($ch);
    curl_close($ch);
    return $curl_status;
  }
</pre>
<div>
  Далее вы сможете использовать <span class="label label-default">sendToObserver([переменная])</span> для того, чтобы получить значения в веб-интерфейсе.
</div>
<h3 id="secret_more">Секретный ключ <small>и $secret_key</small></h3>
<div>
  <p>Секретный ключ представляет из себя десятизначную строку, генерируемую на сервере.
  Вы можете использовать собственную строку. Передав её параметр
  <span class="label label-default">?secret_key=[ваш_секретный_ключ]</span>
  и указав в коде скрипта (<span class="label label-default">$secret_key = '[ваш_секретный_ключ]';</span>).</p>
  <p>Также вы можете указать свою строку, с любым количеством символов.</p>
  <p>В обоих вариантах использования есть минимальная вероятность что данная строка может кем-то использоваться.
  Можно посмотреть количество пользователей с текущим ключом на кнопке копирования ключа (<img src="assets/images/secret_key_button_sample.png">).
  Данное значение показывает общее количество открытых страниц с текущим ключом на всех устройствах.
  Если оно слишком велико, но вы работаете один, или небольшой командой, то проверьте количество открытых вкладок с данной
  страницей, и/или поменяйте секретный ключ.</p>
</div>