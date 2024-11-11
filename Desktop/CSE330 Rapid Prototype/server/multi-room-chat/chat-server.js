// Require the packages we will use:
const http = require("http"),
    fs = require("fs"),
    path = require("path");


const port = 3456;
/* const file = "client.html";
// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html, on port 3456:
const server = http.createServer(function (req, res) {

    fs.readFile(file, function (err, data) {

        if (err) return res.writeHead(500);
        res.writeHead(200);
        res.end(data);
    });
});
server.listen(port); */


const server = http.createServer(function (req, res) {
    let filePath = "." + req.url;
    if (filePath === "./") {
        filePath = "./client.html";
    }

    const extname = String(path.extname(filePath)).toLowerCase();
    const mimeTypes = {
        ".html": "text/html",
        ".js": "application/javascript",
        ".css": "text/css",
    };
    const contentType = mimeTypes[extname] || "application/octet-stream";

    fs.readFile(filePath, function (err, data) {
        if (err) {
            res.writeHead(404);
            res.end("404 Not Found");
            return;
        }
        res.writeHead(200, { "Content-Type": contentType });
        res.end(data);
    });
});
server.listen(port, () => {
    console.log(`Success`);
});


// Import Socket.IO and pass our HTTP server object to it.
const socketio = require("socket.io")(http, {
    wsEngine: 'ws'
});

// Attach our Socket.IO server to our HTTP server to listen
const io = socketio.listen(server);
const users = {};
const rooms = {};
var usernames = {};


io.sockets.on("connection", function (socket) {

    socket.on("login", function (data) {
        socket.username = data.username;
        users[socket.username] = socket;
        usernames[socket.username] = socket.username;
        const createdRooms = Object.keys(rooms).filter(roomName => rooms[roomName].creator === socket.username);
        socket.emit("update_my_rooms", createdRooms);
        console.log(`${data.username} logged in`);
        io.emit("updateUsers", usernames);
        io.sockets.emit("update_active_rooms", rooms);

    });

    // Handle user disconnect
    socket.on("disconnect", function () {
        console.log(`${socket.username} has disconnected.`);

        Object.keys(rooms).forEach(roomName => {
            const room = rooms[roomName];
            if (room && room.users.includes(socket.username)) {
                room.users = room.users.filter(user => user !== socket.username);

                io.to(roomName).emit("update_room_users", {
                    roomName: roomName,
                    users: room.users,
                    creator: room.creator
                });
            }
        });

        delete usernames[socket.username];
        delete users[socket.username];

        io.sockets.emit("updateUsers", usernames);

        socket.broadcast.emit(
            "updateChat",
            "INFO",
            socket.username + " has disconnected"
        );
    });


    //Create a chat room
    socket.on("create_room", function ({ roomName, password }) {
        if (!rooms[roomName]) {
            rooms[roomName] = {
                creator: socket.username,
                users: [socket.username],
                bannedUsers: [],
                kickedUsers: [],
                password: password || ""
            };

            console.log(`Room: "${roomName}" created`);

            io.sockets.emit("update_active_rooms", rooms);

        }
        else {
            io.sockets.emit("create_failed", { message: "Room already exists!" });
        }
    });


    //Join a chat room
    socket.on("join_room_at_server", function ({ roomName, password }) {
        const room = rooms[roomName];
        if (!room) {
            socket.emit("join_failed", { message: "Room not found" });
            return;
        }

        if (room.bannedUsers.includes(socket.username)) {
            socket.emit("join_failed", { message: "You are banned from this room." });
            return;
        }

        if (room.password && room.password !== password) {
            socket.emit("join_failed", { message: "Incorrect password." });
            return;
        }

        socket.join(roomName);
        if (!room.users.includes(socket.username)) {
            room.users.push(socket.username);
        }
        io.to(roomName).emit("update_room_users", { roomName: roomName, users: room.users, creator: room.creator });
    });

    const messages = {};

    //handles message on server side 
    socket.on("message_to_room", function ({ roomName, message, recipient }) {


        if (!messages[roomName]) messages[roomName] = [];


        const timestamp = new Date();

        const messageData = { username: socket.username, message, timestamp: timestamp.toISOString() };

        messages[roomName].push(messageData);

        if (recipient === "everyone") {
            io.to(roomName).emit("message_to_client", { username: socket.username, message, roomName, timestamp: timestamp.toISOString(), });
        } else {
            const recipientSocket = users[recipient];
            if (recipientSocket) {
                recipientSocket.emit("message_to_client", { username: `${socket.username} (private)`, message, roomName, timestamp: timestamp.toISOString() });

            }
            socket.emit("message_to_client", { username: `You (private to ${recipient})`, message, roomName, timestamp: timestamp.toISOString() });
        }
        console.log(`Message from ${socket.username} to ${recipient} in room ${roomName}: ${message}`);
    });


    socket.on("kick_user", function ({ roomName, usernameToKick }) {
        const room = rooms[roomName];

        if (room && room.creator === socket.username) {
            if (room.users.includes(usernameToKick)) {

                room.users = room.users.filter(user => user !== usernameToKick);

                io.to(roomName).emit("update_room_users", { roomName: roomName, users: room.users, creator: room.creator });


                const kickedSocket = users[usernameToKick];
                if (kickedSocket) {
                    kickedSocket.leave(roomName);
                    kickedSocket.disconnect();
                }

            }
        }
    });

    socket.on("ban_user", function ({ roomName, usernameToBan }) {
        const room = rooms[roomName];

        if (room && room.creator === socket.username) {
            if (room.users.includes(usernameToBan)) {
                room.users = room.users.filter(user => user !== usernameToBan);
                room.bannedUsers.push(usernameToBan);


                io.to(roomName).emit("update_room_users", { roomName: roomName, users: room.users, creator: room.creator });

                const bannedSocket = users[usernameToBan];
                if (bannedSocket) {
                    bannedSocket.leave(roomName);
                    bannedSocket.disconnect();
                }
            }
        }
    });

    //Creative Portion
    socket.on("leave_room", function ({ roomName }) {
        const room = rooms[roomName];
        if (room) {
            room.users = room.users.filter(user => user !== socket.username);

            io.to(roomName).emit("update_room_users", {
                roomName: roomName,
                users: room.users,
                creator: room.creator
            });



            socket.leave(roomName);
        }
    });




});

