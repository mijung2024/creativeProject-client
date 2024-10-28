let token = null;

$('#loginBtn').on('click', function (e) {
    e.preventDefault();

    const username = $('#username').val();
    const password = $('#password').val();
    const data = { 'username': username, 'password': password };
    const path = 'login.php';

    fetch(path, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json()) // Use .json() to parse directly as JSON
        .then(jsonResponse => {
            if (jsonResponse.success) {

                $(".loggedinStatus").show();
                $("#greetingMessage").text(`Welcome back, ${jsonResponse.fullname}!`);

                $(".loginForm").hide();
                $(".add_event_btn").show();
                $("#logout_div").show();  // Show logout button on successful login
                token = jsonResponse.token;
                updateCalendar();
            } else {
                $("#loginMessage").text(jsonResponse.msg);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            $("#loginMessage").text("Login failed. Please try again.");
        });
});

$('#registerBtn').on('click', function (e) {
    e.preventDefault();


    const username = $('#reg_username').val();
    const password = $('#reg_password').val();
    const fullName = $('#full_name').val();
    const data = { 'username': username, 'password': password, 'full_name': fullName };
    const path = 'register.php';


    fetch(path, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(jsonResponse => {
            if (jsonResponse.success) {
                $("#registerMessage").text(jsonResponse.msg);
                $('.registerForm').hide();
                $('.loginForm').show();
            } else {
                $("#registerMessage").text(jsonResponse.msg);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            $("#registerMessage").text("Registration failed. Please try again.");
        });
});

$('#showRegisterForm').on('click', function (e) {
    e.preventDefault();
    $('.loginForm').hide();
    $('.registerForm').show();
    $('.loggedinStatus').hide(); // Hide logged-in status when switching forms
});

$('#showLoginForm').on('click', function (e) {
    e.preventDefault();
    $('.registerForm').hide();
    $('.loginForm').show();
    $('.loggedinStatus').hide(); // Hide logged-in status when switching forms
});

$('#logoutBtn').on('click', function (e) {
    e.preventDefault();
    updateCalendar();

    const path = 'logout.php';

    fetch(path)
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                token = null;  // Reset token on logout
                username = null;
                $('#username').val('');
                $('#password').val('');
                // Update UI to reflect the logout
                $('.loginForm').show();
                $('.registerForm').hide();
                $('.loggedinStatus').hide();
                $('#greetingMessage').text("");  // Clear greeting message
                $("#logout_div").hide();  // Hide logout button
                $(".add_event_btn").hide();  // Hide event buttons after logout
            }
        })
        .catch(err => console.error(err));
});
