let token = localStorage.getItem('token') || null;
let userFullName = localStorage.getItem('name') || null;


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
        .then(response => response.json())
        .then(jsonResponse => {
            if (jsonResponse.success) {

                $(".loggedinStatus").show();
                $("#greetingMessage").text(`Welcome back, ${jsonResponse.fullname}!`);

                $(".loginForm").hide();
                $(".add_event_btn").show();
                $("#logout_div").show
                    ();
                token = jsonResponse.token;
                userFullName = jsonResponse.fullname;

                localStorage.setItem('token', token);
                localStorage.setItem('name', userFullName);

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
                token = null;

                username = null;
                $('#username').val('');
                $('#password').val('');

                $('.loginForm').show();
                $('.registerForm').hide();
                $('.loggedinStatus').hide();
                $('#greetingMessage').text("");
                $("#logout_div").hide();
                $(".add_event_btn").hide();
                clearEvents();
                localStorage.removeItem('token');
                s
                localStorage.removeItem('userFullName');
                s


            }
        })
        .catch(err => console.error(err));
});

function authenticateUser(token) {
    const path = 'validate_token.php'; // Server endpoint to validate token

    fetch(path, {
        method: 'POST',
        body: JSON.stringify({ 'token': token }),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(jsonResponse => {
            if (jsonResponse.success) {
                $(".loggedinStatus").show();
                $("#greetingMessage").text(`Welcome back, ${userFullName}!`);
                $(".loginForm").hide();
                $(".add_event_btn").show();
                $("#logout_div").show();
                updateCalendar();


            } else {
                // Clear token if it’s invalid
                localStorage.removeItem('token');
                localStorage.removeItem('name');
            }
        })
        .catch(err => {
            console.error('Token validation error:', err);
        });
}