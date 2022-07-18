// var app = require('express')();
// var http = require('http').Server(app);
// var io = require('socket.io')(http);
// var moment = require('moment');

// /*MySql connection*/
// var connection  = require('express-myconnection'),
// mysql = require('mysql');

// // app.get('', function(req, res){
// //   res.sendFile(__dirname + '/index.html');
// // });

// var userList = [];
// var typingUsers = {};


// app.use(
//     connection(mysql,{
//         host     : 'localhost',
//         user     : 'root',
//         password : '',
//         database : 'apruebo',
//         debug    : true //set true if you wanna see debug logger
//     },'request')
// );


// var con = mysql.createConnection({
//     host: "localhost",
//     user: "root",
//     password: "",
//     database : 'apruebo',
// });


// con.connect(function(err) {
//     if (err) throw err;
//     console.log("Connected!");
// });


// app.get('/', function(req, res){
//     var room = '5-6';
//     //var sql = 'SELECT * FROM chats where room = "'+room+'"';
//     var sql = 'SELECT * FROM chats';
//     con.query(sql, function (err, result) {
//         if (err)
//         {
//             throw  err;
//             res.send({'message':err});
//         } 
//         else
//         {
//             res.send(result);
//         }
//     });
// });


// io.on('connection', function(clientSocket){
//     console.log(clientSocket.id);

//     console.log('a user connected');

//     clientSocket.on('disconnect', function(){
//        console.log('user disconnected');
//     });


//     clientSocket.on('joinroom', function(room,userid) {
//         clientSocket.room = room;
//         clientSocket.join(room);
//         console.log(clientSocket.id);
//         console.log('Joined room');
//         clientSocket.emit("joinedroom", room,userid);
//     });


//     clientSocket.on('chatMessage', function(sender_id,reciver_id,message,room,datetime){
//         console.log(message);
//         console.log('/.'+room+'./');
//         var sql =   "INSERT INTO chats (sender_id, receiver_id ,message,room,is_read,status,created) VALUES ('"+sender_id+"','"+reciver_id+"','"+message+"','"+room+"','0','1','"+datetime+"')";
//         con.query(sql, function (err, result) {
//             if (err){
//                 // clientSocket.broadcast.to(room).emit('saveMessage',err);
//                 throw  err;
//             }else{
//                 console.log('Insertion done');
//                 clientSocket.broadcast.to(room).emit('newChatMessage', sender_id, message, datetime);
//             }
//         });
//     });


//     clientSocket.on('getMessage', function(room,userid) {
//         var sql = 'SELECT * FROM chats where room = "'+room+'"';
//         con.query(sql, function (err, result) {
//             if (err){
//                 io.to(room).emit('getMessage',err,userid);
//                 throw  err;
//             }else{
//                 console.log(JSON.stringify(result));
//                 io.to(room).emit('getMessage',result,userid);
//             }
//         });
//     });

// });


// http.listen(9091, function(){
//     console.log('listening on *:9091');
// });




var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var moment = require('moment');

/*MySql connection*/
var connection  = require('express-myconnection'),
mysql = require('mysql');

app.get('', function(req, res){
  res.sendFile(__dirname + '/index1.html');
});

var userList = [];
var typingUsers = {};

app.use(
    connection(mysql,{
        host     : 'localhost',
        user     : 'root',
        password : '',
        database : 'social',
        charset : "utf8mb4_unicode_ci",
        debug    : true //set true if you wanna see debug logger
    },'request')
    );


var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database : 'social',
    charset : "utf8mb4_unicode_ci"
});


con.connect(function(err) {
    if (err) throw err;
    console.log("Connected!");
});


/*app.get('/', function(req, res){
    var room = '5-6';
    //var sql = 'SELECT * FROM chats where room = "'+room+'"';
    var sql = 'SELECT * FROM chats';
    con.query(sql, function (err, result) {
        if (err)
        {
            throw  err;
            res.send({'message':err});
        } 
        else
        {
            res.send(result);
        }
    });
});*/


io.on('connection', function(clientSocket){

    console.log(clientSocket.id);

    console.log('a user connected');

    clientSocket.on('disconnected', function(chat_id){
    clientSocket.leave(chat_id);
     });

    clientSocket.on('joinroom', function(chat_id) {
        clientSocket.room = chat_id;
        clientSocket.join(chat_id);
        console.log(clientSocket.id);
        console.log('Joined room');
        io.to(chat_id).emit('joined');
    });

    /*clientSocket.on('chatMessage', function(sender_id,reciver_id,message,room,datetime){
        console.log(message);
        console.log('/.'+room+'./');
        var sql =   'INSERT INTO chats (sender_id, receiver_id ,room, message ,seen_status,created_date) VALUES ("'+sender_id+'","'+reciver_id+'","'+room+'","'+message+'",0,"'+datetime+'")';
        //var sql =   "INSERT INTO chats (sender_id, receiver_id ,message,room,is_read,status,created,modified) VALUES ('"+sender_id+"','"+reciver_id+"','"+message+"','"+room+"','0','1','"+datetime+"','"+datetime+"')";
        con.query(sql, function (err, result) {
            if (err){
                // clientSocket.broadcast.to(room).emit('saveMessage',err);
                throw  err;
            }else{

                console.log( sender_id );

                console.log('Insertion done');
                //io.to(room).emit('newChatMessage', sender_id, message, datetime);
                //clientSocket.to(room).emit('newChatMessage', {sender_id, message, datetime});
                //console.log(result);
                //console.log('Insertion done');
                //clientSocket.broadcast.emit('getMessage',room);
                // io.emit('getMessage',{room});
                //io.to(room).emit('newChatMessage', {sender_id, message, datetime});
                //io.in(room).emit('newChatMessage', sender_id, message, datetime);
                // clientSocket.to(room).emit('newChatMessages', {sender_id, message, datetime});
                //clientSocket.emit('newChatMessage', {sender_id, message, datetime});
                io.to(room).emit('newChatMessages', {sender_id, message, datetime});
                  console.log('New Message Get');
              }
          });
      });*/

      clientSocket.on('getNewMsg', function(chat_id){
                var data =   'SELECT * FROM `chats` where chat_id = "'+chat_id+'"  ORDER BY `chats`.`created_at` DESC  LIMIT 1;  ';
                con.query(data, function (err, result) {
                    if (err){
                        // clientSocket.broadcast.to(room).emit('saveMessage',err);
                        throw  err;
                    }else{
                            io.in(chat_id).emit("recieveMsg", result[0]);
                            // io.to(chat_id).emit("recieveMsg", {
                            //     result
                            // });
                    }
                });
            });


         
 /*clientSocket.on('webMessages', function(info) {
    console.log(info)
        var sql = 'SELECT * FROM chats where room = "'+info.room+'"';
        con.query(sql, function (err, result) {
            if (err){
                io.to(info.room).emit('getMessage',err,info.userid);
                throw  err;
            }else{
                var res = [];
                result = JSON.stringify(result);
                result = JSON.parse(result);
                result.forEach(function(value,i){
                    value.created_date = moment(value.created_date).format('YYYY-MM-DD hh:mm:ss A');
                })
                console.log(result);
                console.log("String",JSON.stringify(result));
                clientSocket.broadcast.to(info.room).emit('messageList', result);
                // clientSocket.on("messageList",(result)=>{
                //     console.log("result",result)
                // })
                // clientSocket.broadcast.to(room).emit('messageList', result,userid);
                // io.to(info.room).emit('getMessage',result,info.userid);
            }
        });
    });*/


    clientSocket.on('getMessage', function(chat_id,offset = 0,limit= 100) {
        // var sql = 'UPDATE chats set seen_status = 1 where room = "'+room+'"';
        // con.query(sql, function (err, result) {
        //     if (err){
        //         io.to(room).emit('getMessage',err,userid);
        //         throw  err;
        //     }
        // });
        /*if(!offset){offset = 0;}
        if(!limit){limit = 15;}*/
        var sql = 'SELECT * FROM messages where chat_id = "'+chat_id+'" limit '+offset+','+limit;
        con.query(sql, function (err, result) {
            if (err){
                io.to(room).emit('getMessage',err);
                throw  err;
            }else{
                console.log(result);
                var vakeel  = "punit op in the chat ";
                // var res = [];
                // result = JSON.stringify(result);
                // result = JSON.parse(result);
                // result.forEach(function(value,i){
                //     value.send_date = value.created_date;
                //     value.created_date = moment(value.created_date).format('DD-MM-YYYY hh:mm A');
                // })
                // console.log(result);
                // console.log('String',JSON.stringify(result));
                //io.to(room).emit('getMessage',result,userid);
                io.emit('getMessage',vakeel);
            }
        });
    });

});


http.listen(3000, function(){
    console.log('listening on *:3000');
});
