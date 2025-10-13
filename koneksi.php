<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'review_laptop_lokal');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");

// Function untuk mencegah SQL Injection
function escape_string($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Function untuk validasi input
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function untuk upload gambar
function upload_image($file, $target_dir = "IMG/") {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    // Validasi file
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Validasi tipe file
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Hanya file gambar (JPG, PNG, GIF) yang diperbolehkan'];
    }
    
    // Validasi ukuran file
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 2MB'];
    }
    
    // Generate nama file unik
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $target_dir . $new_filename;
    
    // Buat direktori jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $target_path];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

// Function untuk delete file
function delete_image($filepath) {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// Function untuk format Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Function untuk query dengan error handling
function query_execute($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn));
        return false;
    }
    
    return $result;
}

// Function untuk get single row
function get_single_row($query) {
    $result = query_execute($query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Function untuk get multiple rows
function get_multiple_rows($query) {
    $result = query_execute($query);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Function untuk insert data
function insert_data($table, $data) {
    global $conn;
    
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", array_map('escape_string', array_values($data))) . "'";
    
    $query = "INSERT INTO $table ($columns) VALUES ($values)";
    
    if (query_execute($query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function untuk update data
function update_data($table, $data, $where) {
    $set_values = [];
    foreach ($data as $key => $value) {
        $set_values[] = "$key = '" . escape_string($value) . "'";
    }
    
    $set_string = implode(', ', $set_values);
    $query = "UPDATE $table SET $set_string WHERE $where";
    
    return query_execute($query);
}

// Function untuk delete data
function delete_data($table, $where) {
    $query = "DELETE FROM $table WHERE $where";
    return query_execute($query);
}

// Function untuk close connection (dipanggil di akhir script)
function close_connection() {
    global $conn;
    if ($conn) {
        mysqli_close($conn);
    }
}

// Register shutdown function untuk close connection otomatis
register_shutdown_function('close_connection');
?>