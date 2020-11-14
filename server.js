var app = require('express')();
var http = require('http').createServer(app);
const io = require("socket.io")(http, {
    cors: {
        origin: "http://chat.test",
        methods: ["GET", "POST"],
        allowedHeaders: [{
            "Access-Control-Allow-Origin": "http://chat.test",
            "Access-Control-Allow-Methods": "GET,POST",
            "Access-Control-Allow-Credentials": true
        }],
        credentials: true
    }
});
var users = [];
var Redis = require('ioredis');
var redis = new Redis();



http.listen(3000, () => {
    console.log('listening on *:3000');
});


redis.subscribe('private-channel', function(){
    console.log('subscribed to private channel');
});


redis.on('message', function (channel, message){
    console.log("Kanal:"+ channel);
    console.log("Mesaj:"+ message);
    message = JSON.parse(message);
    if(channel === 'private-channel'){
        let data = message.data.data;
        let receiver_id = data.receiver_id;
        let event = message.event;

        io.to(`${users[receiver_id]}`).emit(channel + ':' + event, data);

    }
});

io.on('connection', (socket) => {
    socket.on('user_connected', (user_id) => {
        users[user_id] = socket.id;
        io.emit('updateUserStatus', users);
        console.log("Socket Connected: "+user_id);
    });

    socket.on('disconnect', () => {
        var i = users.indexOf(socket.id);
        users.splice(i, 1, 0);
        io.emit('updateUserStatus', users);
        console.log(users);
    });
});
