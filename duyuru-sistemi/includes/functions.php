<?php
// includes/functions.php
session_start();

function checkAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit;
    }
}

function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function uploadImage($file) {
    $target_dir = "../assets/uploads/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }
    return false;
}
?>