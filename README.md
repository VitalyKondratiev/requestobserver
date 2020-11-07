# Requirements
Server with PHP and NodeJS 

# Installation
- Install Docker and Docker Compose on your server
- Add host `requestobserver.local` to your host file (you can change in in `.env` file)
- Launch `docker-compose up` in root directory of this project

# Configuration
For configurate this tool, you need change example values in `/config.yml` file
- `node_port` - socket.io port
# Usage
For observing your data, launch webpage with GET parameter `?secret_key`, you can use any string as value this parameter.  
In script, which you need observe, you can create function as below, and use it `sendToObserver([your_data])` from your code.
```php
  function sendToObserver($data) {
    $requestUrl = '[requestobserver_webpage]';
    $secret_key = '[your_secret_key]';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl."?page=http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]".'&secret_key='.$secret_key);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array($data)));
    $curl_status = curl_exec($ch);
    curl_close($ch);
    return $curl_status;
  }
```

`requestobserver_webpage` - webpage, which you open in browser.  
`your_secret_key` - value of GET parameter `?secret_key`.