var http = require('http');
var io = require('socket.io');
var querystring = require('querystring');
var url = require('url');
var yaml = require("js-yaml");
var fs = require('fs');

var config = yaml.safeLoad(fs.readFileSync(__dirname + '\\config.yml', 'utf8'));
var _port = config.node_port;

function getClientAddress(request) {
  return (request.headers['x-forwarded-for'] || '').split(',')[0] || request.connection.remoteAddress;
};

function processPost(request, response, callback) {
  var queryData = "";
  if (typeof callback !== 'function') return null;

  if (request.method == 'POST') {
    request.on('data', function (data) {
      queryData += data;
      if (queryData.length > 1e6) {
        queryData = "";
        response.writeHead(413, { 'Content-Type': 'text/plain' }).end();
        request.connection.destroy();
      }
    });

    request.on('end', function () {
      request.post = querystring.parse(queryData);
      request.ip = getClientAddress(request);
      callback();
    });

  } else {
    response.writeHead(405, { 'Content-Type': 'text/plain' });
    response.end();
  }
}

io = io.listen(http.createServer(function (request, response) {
  response.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
  response.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
  response.setHeader('Access-Control-Allow-Credentials', true);
  if (request.method == 'POST') {
    processPost(request, response, function () {
      post = request.post;
      get = url.parse(request.url, true).query;
      result = { 'post': post, 'page': get.page, 'ip': get.client_ip, 'time': new Date() };
      /*console.log('------------------------');
      console.log(result);
      console.log('------------------------');/**/
      data_to_send = encodeURIComponent(JSON.stringify(result));
      io.sockets.to(get.secret_key).emit("result", data_to_send);
      response.writeHead(200, "OK", { 'Content-Type': 'text/plain' });
      response.end();
    });
  } else {
    response.writeHead(200, "OK", { 'Content-Type': 'text/plain' });
    response.end();
  }
}).listen(_port, function () { console.log('Start listening port: ' + _port); }));;

var usersCountByKey = {}; //Количество пользователей на каждом секретном ключе

io.on('connection', function (socket) {
  get = url.parse(socket.handshake.headers.referer, true).query;
  socket.secret_key = get.secret_key;
  socket.join(socket.secret_key);
  if (usersCountByKey[socket.secret_key] == undefined)
    usersCountByKey[socket.secret_key] = 1;
  else
    usersCountByKey[socket.secret_key]++;
  io.sockets.to(get.secret_key).emit("users_count", usersCountByKey[socket.secret_key]);
  socket.on('disconnect', function(){
    usersCountByKey[socket.secret_key]--;
		io.sockets.to(get.secret_key).emit("users_count", usersCountByKey[socket.secret_key]);
  });
});
