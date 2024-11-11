var socketio = io();
let username = "";
let creator_of_room = "";

var userlist = document.getElementById("active_users_list");
var roomlist = document.getElementById("active_rooms_list");
var message = document.getElementById("message_input");
var sendMessageBtn = document.getElementById("send_message_btn");
var roomInputName = document.getElementById("room_input_name");
var chatDisplay = document.getElementById("chatlog");

let myRooms = [];
let activeRooms = [];
var currentRoom = "";

document.addEventListener("DOMContentLoaded", function () {
    const loginButton = document.getElementById("login_button");

    loginButton.addEventListener("click", logIn);

});

//Handles login 
function logIn() {
    const input = document.getElementById("username_input");
    enteredUsername = input.value.trim();
    if (enteredUsername) {
        socketio.emit("login", { username: enteredUsername });
        username = enteredUsername;

        document.getElementById("login-overlay").style.display = "none";
        document.getElementById("user_display").innerText = "Hi, " + username;

        document.getElementById("message_input").disabled = false;
        document.getElementById("room_input_name").disabled = false;
        document.getElementById("room_add").disabled = false;
        document.querySelector(".send_message_btn").disabled = false;
    }
}
function createRoom() {
    let roomName = roomInputName.value.trim();
    const password = prompt("Enter a password for this room (leave blank for no password):");
    if (roomName != "") {
        socketio.emit("create_room", { roomName, password });
        roomInputName.value = "";
    }
}
socketio.on("create_failed", function (data) {
    alert(alert(data.message));
});

socketio.on("update_active_rooms", function (rooms) {
    console.log("Active Rooms update:", rooms);

    const roomList = document.getElementById("active_rooms_list");
    roomList.innerHTML = "";

    Object.keys(rooms).forEach(roomName => {
        const roomElement = document.createElement("div");
        roomElement.classList.add("room_card");
        roomElement.id = roomName;
        let creator = rooms[roomName].creator;
        roomElement.innerHTML = `
        <div>
            <span class="room_name">Room: ${roomName}</span>
            <br />
            <span class="room_author">Created by: ${rooms[roomName].creator}</span>
        </div>`;

        roomList.appendChild(roomElement);


        // When clicked, join the room
        roomElement.onclick = () => joinRoom(roomName, creator);
    });
});

function joinRoom(roomName, creator) {
    leaveRoom();
    currentRoom = roomName;
    creator_of_room = creator;
    chatDisplay.innerHTML = "";

    const password = prompt("Enter a password for this room (leave blank for no password):");

    document.getElementById("room_name").textContent = `${roomName}`;
    document.getElementById("room_name_info").textContent = `Created by: ${creator}`;

    socketio.emit("join_room_at_server", { roomName, password });

}

socketio.on("join_failed", function (data) {
    alert(alert(data.message));
});

socketio.on("update_room_users", function (data) {
    const { roomName, users, creator } = data;
    const activeUsersList = document.getElementById("active_users_list");
    activeUsersList.innerHTML = "";

    const recipientSelect = document.getElementById("recipient_select");
    recipientSelect.innerHTML = "<option value='everyone'>Everyone</option>";

    users.forEach(user => {
        const userElement = document.createElement("div");
        userElement.id = user;
        userElement.classList.add("user_card");
        userElement.innerText = user;

        const kickButton = document.createElement("button");
        kickButton.innerText = "Kick";
        kickButton.onclick = function () {
            kickUser(user);
        };

        const banButton = document.createElement("button");
        banButton.innerText = "Ban";
        banButton.onclick = function () {
            banUser(user);
        };

        if (user !== username && username === creator) {
            userElement.appendChild(banButton);
            userElement.appendChild(kickButton);
        }

        activeUsersList.appendChild(userElement);

        if (user !== username) {
            const option = document.createElement("option");
            option.value = user;
            option.textContent = user;
            recipientSelect.appendChild(option);
        }
    });
});



function sendMessage() {
    const messageText = message.value.trim();
    if (!username) {
        alert("Please log in first!");
        return;
    }
    if (!currentRoom) {
        alert("Join a room to chat!");
        return;
    }
    if (messageText != "") {
        const recipient = document.getElementById("recipient_select").value;

        socketio.emit("message_to_room", { roomName: currentRoom, message: messageText, recipient });
        message.value = "";
    }
}

// Client-side message display
socketio.on("message_to_client", function (data) {
    const { username, message, roomName, timestamp } = data;

    if (roomName === currentRoom) {
        const messageElement = document.createElement("div");
        messageElement.classList.add("message");

        const formattedTimestamp = new Date(timestamp).toLocaleTimeString();


        if (username.includes("(private)")) {
            messageElement.classList.add("private-message");

            messageElement.innerHTML = `
            <strong>${username}</strong>: ${message}
            <span class="timestamp">${formattedTimestamp}</span>
        `;
            const inboxMessage = document.createElement("div");
            inboxMessage.classList.add("inbox-message");
            inboxMessage.innerHTML = `
                <span>User ${username} from room ${roomName}</span>
                <button onclick="deleteInboxMessage(this)">Delete</button>
            `;
            document.getElementById("inbox_messages").appendChild(inboxMessage);

        }
        else {
            messageElement.innerHTML = `
            <strong>${username}</strong>: ${message}
            <span class="timestamp">${formattedTimestamp}</span>
        `;
        }



        chatDisplay.appendChild(messageElement);
        chatDisplay.scrollTop = chatDisplay.scrollHeight;
    }
});

function deleteInboxMessage(button) {
    const inboxMessage = button.parentElement;
    inboxMessage.remove();
}
message.addEventListener("keyup", function (event) {
    const messageText = message.value.trim();
    if (event.key === "Enter" && messageText != "") {
        sendMessageBtn.click();
    }
});
function kickUser(usernameToKick) {
    socketio.emit("kick_user", { roomName: currentRoom, usernameToKick: usernameToKick });
}

function banUser(usernameToBan) {
    socketio.emit("ban_user", { roomName: currentRoom, usernameToBan: usernameToBan });
}

//creative portion
function leaveRoom() {
    if (currentRoom) {
        socketio.emit("leave_room", { roomName: currentRoom });
        currentRoom = "";
        chatDisplay.innerHTML = "";
    }
}

