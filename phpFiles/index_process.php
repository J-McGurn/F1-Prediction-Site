<?php
session_start();

// Connect to MySQL
$conn = new mysqli('localhost', 'root', 'BillySQL945!', 'f1predictions');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

// Determine action
$action = $_POST['action'];

if ($action == 'signup') {
    // Get form data
    $username = $_POST['username'];
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate and process signup
    if (strlen($username) > 15) {
        $errors['username'] = "Username must be 15 characters or less.";
    }
    if (preg_match('/[^a-zA-Z0-9]/', $username)) {
        $errors['username'] = "Username may only contain letters and numbers.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($errors)) {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $errors['username'] = "Username is already taken.";
        }

        // Check if email already exists
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $errors['email'] = "Email is already registered.";
            }
        }

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database if no errors
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO users (username, fname, sname, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $fname, $sname, $email, $password_hash);

            if ($stmt->execute()) {
                // Optionally clear session if needed
                session_unset();
                session_destroy();
                session_start();

                $sql = "SELECT * FROM users WHERE username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fname'] = $user['fname'];
                    header('Location: home.php'); // Adjust path if needed
                    exit();
                } else {
                    $errors[] = "Error: id not found" . $stmt->error;
                }
            } else {
                $errors[] = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    // Redirect back to index.php with errors
    $query = http_build_query(['signup_error' => implode(' ', $errors)]);
    header('Location: index.php?' . $query);
    exit();
} elseif ($action == 'login') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate form data
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Prepare and execute login query
        $stmt = $conn->prepare("SELECT user_id, username, password, fname FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $db_username, $password_hash, $fname);
        $stmt->fetch();
        $stmt->close();

        // Verify password
        if (password_verify($password, $password_hash)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['fname'] = $fname;
            
            // Redirect to home page
            header('Location: home.php'); // Adjust path if needed
            exit();
        } else {
            $errors[] = "Invalid username or password.";
        }
    }

    // Store errors in session and redirect back to index.php
    $_SESSION['login_errors'] = $errors;
    header('Location: index.php');
    exit();
}

// Close connection
$conn->close();
?>
