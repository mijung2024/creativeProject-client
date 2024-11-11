(function () {
    Date.prototype.deltaDays = function (c) {
        return new Date(this.getFullYear(), this.getMonth(), this.getDate() + c);
    };

    Date.prototype.getSunday = function () {
        return this.deltaDays(-1 * this.getDay());
    };
})();

function Week(c) {
    this.sunday = c.getSunday();
    this.nextWeek = function () {
        return new Week(this.sunday.deltaDays(7));
    };
    this.prevWeek = function () {
        return new Week(this.sunday.deltaDays(-7));
    };
    this.contains = function (b) {
        return this.sunday.valueOf() === b.getSunday().valueOf();
    };
    this.getDates = function () {
        var b = [];
        for (var a = 0; a < 7; a++) b.push(this.sunday.deltaDays(a));
        return b;
    };
}

function Month(c, b) {
    this.year = c;
    this.month = b;
    this.nextMonth = function () {
        return new Month(this.year + Math.floor((this.month + 1) / 12), (this.month + 1) % 12);
    };
    this.prevMonth = function () {
        return new Month(this.year + Math.floor((this.month - 1) / 12), (this.month + 11) % 12);
    };
    this.getDateObject = function (a) {
        return new Date(this.year, this.month, a);
    };
    this.getWeeks = function () {
        var a = this.getDateObject(1);
        var b = this.nextMonth().getDateObject(0);
        var c = [];
        var currentWeek = new Week(a);
        c.push(currentWeek);
        while (!currentWeek.contains(b)) {
            currentWeek = currentWeek.nextWeek();
            c.push(currentWeek);
        }
        return c;
    };
}

var currentMonth = new Month(new Date().getFullYear(), new Date().getMonth());

// Function to update the calendar in the DOM
function updateCalendar() {

    var weeks = currentMonth.getWeeks();
    var calendarBody = document.getElementById("calendar-body");
    calendarBody.innerHTML = ''; // Clear previous calendar

    weeks.forEach(function (week) {
        var weekRow = document.createElement('tr');
        var days = week.getDates();

        days.forEach(function (day) {



            const dayOfMonth = day.getDate();
            const cellId = `date-${day.getFullYear()}-${day.getMonth() + 1}-${dayOfMonth}`; // Unique ID for each cell
            const dateCell = document.createElement('td');
            dateCell.id = cellId;
            dateCell.textContent = dayOfMonth;

            dateCell.addEventListener('click', function () {


                if (token) {
                    openEventModal(day); // Function to handle event creation/modification
                    document.getElementById("save-changes").style.display = 'flex';
                    document.getElementById("edit-changes").style.display = 'none';
                } else {
                    alert("Please log in to add or view events.");

                }

            });

            // Highlight today's date
            const today = new Date();
            if (day.toDateString() === today.toDateString()) {
                dateCell.classList.add('today');
            }
            weekRow.appendChild(dateCell);
        });

        calendarBody.appendChild(weekRow);
    });

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    document.getElementById('month-year').textContent = `${monthNames[currentMonth.month]} ${currentMonth.year}`;

    fetchEvents();
}

// Event listeners for navigation buttons
document.getElementById("next-month").addEventListener("click", function () {
    currentMonth = currentMonth.nextMonth();
    updateCalendar(); // Update the calendar display
});

document.getElementById("prev-month").addEventListener("click", function () {
    currentMonth = currentMonth.prevMonth();
    updateCalendar(); // Update the calendar display
});


// Fetch events from the server
function fetchEvents() {

    document.querySelectorAll('.event').forEach(event => event.remove());

    const data = { 'token': token }; // Adjust as necessary for your token

    fetch('events.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                response.events.forEach(event => {
                    const eventDate = new Date(event.date + 'Z'); // Treat as UTC

                    const day = eventDate.getUTCDate(); // Use getUTCDate to avoid local timezone issues
                    const month = eventDate.getUTCMonth() + 1; // Months are zero-based
                    const year = eventDate.getUTCFullYear();
                    const cellId = `date-${year}-${month}-${day}`;

                    const dateCell = document.getElementById(cellId);

                    if (dateCell) {

                        const eventDiv = document.createElement('div');
                        eventDiv.className = `event ${event.type}`;
                        eventDiv.setAttribute('data-event-id', event.id); // Add this line

                        // Text container for time and event name
                        const eventText = document.createElement('span');
                        eventText.className = 'event-text';
                        eventText.textContent = `${event.time} ${event.name}`;

                        // Icons container
                        const iconContainer = document.createElement('span');
                        iconContainer.className = 'icon-container';

                        // Edit icon
                        const editIcon = document.createElement('i');
                        editIcon.className = 'bi bi-pencil-square edit-icon';
                        editIcon.title = 'Edit';
                        editIcon.addEventListener('click', function (e) {
                            e.stopPropagation();
                            editEvent(event.id);
                        });

                        // Delete icon
                        const deleteIcon = document.createElement('span');
                        deleteIcon.className = 'delete-icon';
                        deleteIcon.title = 'Delete';
                        deleteIcon.innerHTML = '&times;';
                        deleteIcon.addEventListener('click', function (e) {
                            e.stopPropagation();
                            deleteEvent(event.id);
                        });

                        iconContainer.appendChild(editIcon);
                        iconContainer.appendChild(deleteIcon);
                        eventDiv.appendChild(eventText);
                        eventDiv.appendChild(iconContainer);

                        dateCell.appendChild(eventDiv);


                        if (event.duration > 1) {
                            const eventDate = new Date(event.date + 'Z'); // Treat as UTC

                            const day = eventDate.getUTCDate(); // Use getUTCDate to avoid local timezone issues
                            const month = eventDate.getUTCMonth() + 1; // Months are zero-based
                            const year = eventDate.getUTCFullYear();
                            const cellId = `date-${year}-${month}-${day}`;

                            const dateCell = document.getElementById(cellId);





                            for (let i = 1; i < event.duration; i++) {
                                const nextDate = new Date(eventDate);
                                nextDate.setDate(eventDate.getDate() + i);
                                const nextCellId = `date-${nextDate.getFullYear()}-${nextDate.getMonth() + 1}-${nextDate.getDate()}`;
                                const nextCell = document.getElementById(nextCellId);

                                if (nextCell) {
                                    const continuedEventDiv = document.createElement('div');
                                    continuedEventDiv.className = `event continued ${event.type}`; // Add continued class
                                    continuedEventDiv.textContent = `${event.time} ${event.name}`; // Display same text
                                    nextCell.appendChild(continuedEventDiv);
                                }
                            }
                        }
                    }
                });
            } else {
                console.error(response.msg);
            }
        })
        .catch(() => {
            console.error("Failed to fetch events.");
        });
}


// Initial call to set up the calendar
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('token');
    if (token) {
        authenticateUser(token);
    }
    else {
        updateCalendar();
    }
});
function clearEvents() {
    document.querySelectorAll('.event').forEach(event => event.remove());
}
