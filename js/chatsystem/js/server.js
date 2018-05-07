
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpBuyerSellerChat
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

var app = require('http').createServer(function (req, res) {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('okay');
});

var io = require('socket.io')(app);
var roomUsers = {};

app.listen(1360, function () {
    console.log('Listening');
});

io.on('connection', function (socket) {
    socket.on('newUserConneted', function (details) {
      if (details.sender === 'admin') {
        var index = details.sender+'_'+details.adminId;
        roomUsers[index] = socket.id;
      } else if (details.sender === 'customer') {
        var index = details.sender+'_'+details.customerId;
        roomUsers[index] = socket.id;
        Object.keys(roomUsers).forEach(function (key, value) {
            if (key === 'admin_'+details.receiver) {
              receiverSocketId = roomUsers[key];
              socket.broadcast.to(receiverSocketId).emit('refresh admin chat list', details);
            }
        });
      }
    });

    socket.on('newCustomerMessageSumbit', function (data) {
      var isSupportActive = true;
      if (typeof(data) !== 'undefined') {
        Object.keys(roomUsers).forEach(function (key, value) {
            if (key === 'admin_'+data.receiver) {
              isSupportActive = true;
              receiverSocketId = roomUsers[key];
              socket.broadcast.to(receiverSocketId).emit('customerMessage', data);
            }
        });
        if (!isSupportActive) {
          receiverSocketId = roomUsers['customer_'+data.sender];
          socket.broadcast.to(receiverSocketId).emit('supportNotActive', data);
        }
      }
  });
  socket.on('newAdminMessageSumbit', function (data) {
      if (typeof(data) !== 'undefined') {
       Object.keys(roomUsers).forEach(function (key, value) {
            if (key === 'customer_'+data.receiver) {
              receiverSocketId = roomUsers[key];
              socket.broadcast.to(receiverSocketId).emit('adminMessage', data);
            }
        });
      }
  });
  socket.on('updateStatus', function (data) {
      var isSupportActive = true;
      if (typeof(data) !== 'undefined') {
        Object.keys(roomUsers).forEach(function (key, value) {
            if (key === 'admin_'+data.receiver) {
              receiverSocketId = roomUsers[key];
              socket.broadcast.to(receiverSocketId).emit('customerStatusChange', data);
            }
        });
      }
  });

  socket.on('admin status changed', function (data) {
      if (typeof(data) !== 'undefined') {
       Object.keys(roomUsers).forEach(function (key, value) {
        Object(data.receiverData).forEach(function (k) {
            if (key === 'customer_'+k.customerId) {
                receiverSocketId = roomUsers[key];
                socket.broadcast.to(receiverSocketId).emit('adminStatusUpdate', data.status);
            }
            });
        });
      }
  });
});
