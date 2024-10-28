function addEvent() {
    const eventName = document.getElementById("event-name").value;
    const eventDate = document.getElementById("event-date").value;
    const eventType = document.getElementById("selected-event-type").value;
    const duration = document.getElementById("duration").value;

    if (!eventName || !eventDate || !eventType || !duration) {
        alert("Please fill in all fields.");
        return;
    }

    const data = {
        name: eventName,
        date: eventDate,
        type: eventType,
        duration: parseInt(duration),
        token: token // Assuming CSRF protection is implemented
    };

    fetch("add_event.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                updateCalendar(); // Refresh the calendar to show the new event
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
});