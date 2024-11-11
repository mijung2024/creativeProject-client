function addEvent() {
    const eventName = document.getElementById("event-name").value;
    const eventDate = document.getElementById("event-date").value;
    const eventType = document.getElementById("selected-event-type").value;
    const duration = document.getElementById("duration").value;
    document.getElementById("save-changes").style.display = 'flex';
    document.getElementById("edit-changes").style.display = 'none';


    if (!eventName || !eventDate || !eventType || !duration) {
        alert("Please fill in all fields.");
        return;
    }

    const data = {
        name: eventName,
        date: eventDate,
        type: eventType,
        duration: parseInt(duration),
        token: token
    };

    fetch("add_event.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                updateCalendar();
            } else {
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

// Attach this function to the 'submit' button in the modal
document.querySelector(".btn.btn-primary").addEventListener("click", function (event) {
    event.preventDefault();
    addEvent();
    document.getElementById('event-modal').style.display = 'none';

});

function deleteEvent(eventId) {
    fetch('delete_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: eventId, token: token })
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                updateCalendar(); // Refresh calendar to remove deleted event
            } else {
                console.error(response.msg);
            }
        })
        .catch(() => console.error("Failed to delete event."));
}
function editEvent(eventId) {
    console.log("Fetching event with ID:", eventId);

    // Fetch event details using POST method
    fetch('get_event.php', {
        method: 'POST', // Change to POST
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: eventId }) // Send event ID
    })
        .then(response => response.json())
        .then(data => { // Changed to 'data' to avoid confusion with event object
            if (data.success) {
                // Set the values in the modal
                document.getElementById("event-id").value = eventId;
                document.getElementById("event-name").value = data.event.name;
                document.getElementById("event-date").value = new Date(data.event.date).toISOString().substring(0, 16);
                document.getElementById("selected-event-type").value = data.event.type;
                document.getElementById("duration").value = data.event.duration;

                document.getElementById("save-changes").style.display = "none";
                document.getElementById("edit-changes").style.display = "block";

                // Show the modal
                document.getElementById("event-modal").style.display = "flex";
            } else {
                console.error(data.msg);
            }
        })
        .catch(error => {
            console.error("Failed to fetch event details:", error);
        });
}

document.getElementById("edit-changes").addEventListener("click", function () {
    const eventId = document.getElementById("event-id").value;
    const eventNameUpdated = document.getElementById("event-name").value;
    const eventDateUpdated = document.getElementById("event-date").value;
    const eventTypeUpdated = document.getElementById("selected-event-type").value;
    const durationUpdated = document.getElementById("duration").value;

    if (!eventNameUpdated || !eventDateUpdated || !eventTypeUpdated || !durationUpdated) {
        alert("Please fill in all fields.");
        return;
    }

    const updatedEventData = {
        id: eventId,
        name: eventNameUpdated,
        date: eventDateUpdated,
        type: eventTypeUpdated,
        duration: parseInt(durationUpdated),
        token: token
    };

    fetch('update_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedEventData)
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                updateCalendar(); // Refresh calendar with updated event
                document.getElementById("event-modal").style.display = 'none';
            } else {
                console.error("Failed to update event:", response.msg);
            }
        })
        .catch(() => console.error("Failed to update event."));
});

function saveEventChanges() {
    const eventId = document.getElementById('event-id').value;
    const updatedData = {
        id: eventId,
        name: document.getElementById('event-name').value,
        date: document.getElementById('event-date').value,
        type: document.getElementById('event-type').value,
        token: token
    };

    fetch('update_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedData)
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                updateCalendar();
                document.getElementById('event-modal').style.display = 'none';
            } else {
                console.error("Error updating event:", response.msg);
            }
        })
        .catch(error => {
            console.error("Failed to update event:", error);
        });
}
function clearEvents() {
    const eventElements = document.querySelectorAll('.event');
    eventElements.forEach(event => event.remove());
}
