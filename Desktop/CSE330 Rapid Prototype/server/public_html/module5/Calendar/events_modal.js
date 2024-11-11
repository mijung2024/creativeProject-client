
function toggleDropdown() {
    const dropdownMenu = document.querySelector(".dropdown-menu");
    dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
}

// Set selected event type
function selectEventType(value) {
    document.getElementById("selected-event-type").value = value;
    document.querySelector(".dropdown-toggle").innerText = value.charAt(0).toUpperCase() + value.slice(1);
    toggleDropdown();
}


function openEventModal(date = new Date()) {

    const eventModal = document.getElementById('event-modal');
    eventModal.style.display = 'flex';

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

    document.getElementById('event-date').value = formattedDateTime;
}

document.getElementById('close-modal').onclick = function () {
    document.getElementById('event-modal').style.display = 'none';
};

window.onclick = function (event) {
    if (event.target == document.getElementById('event-modal')) {
        document.getElementById('event-modal').style.display = 'none';
    }
};
