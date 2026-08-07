<?php
function enforce_admin_access($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Basic check: Ensure user_id exists in session
    if (empty($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    $user_id = (int)$_SESSION['id'];

    // 2. Fetch fresh user data directly from the database
    $stmt = $conn->prepare("SELECT admin, name, email FROM user WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // 3. Handle deleted/missing user records
    if (!$user) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    // 4. SYNCHRONIZE SESSION: Overwrite session data with fresh DB values
    $_SESSION['admin'] = (int)$user['admin'];
    $_SESSION['name']  = $user['name'];
    $_SESSION['email'] = $user['email'];

    // 5. Enforce access control based on updated DB state
    if ($_SESSION['admin'] !== 1) {
        // Demoted user: Keep them logged in as standard user or kick them out
        header("Location: ../index.php");
        exit();
    }
}