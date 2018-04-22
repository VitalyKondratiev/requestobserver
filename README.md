# Requirements
Server with PHP and NodeJS 

# Installation
- Install NodeJS and PHP on your server
- Change directory to root catalog of this project
- Install NodeJS requirements (`npm install`)
- Install Composer requirements (`composer install`)

# Configuration
For configurate this tool, you need change example values in `/config.yml` file
- `node_protocol` - protocol for NodeJS script
- `node_address` - domain for NodeJS script
- `node_port` - socket.io port
# Usage
Start NodeJS part of this project 
```bash
npm run start
```
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