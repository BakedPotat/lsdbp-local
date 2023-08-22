<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Conexión a la base de datos
    $conn = new mysqli("192.168.18.20", "lsdbp", "Coope2022", "test");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    // Buscar el usuario en la base de datos
    $sql = "SELECT id, username, password FROM usuarios WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            header("Location: index.php");
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
    
    $conn->close();
}
?>

<form method="POST" action="">
    <input type="text" name="username" placeholder="Usuario" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>
    <button type="submit">Iniciar sesión</button>
</form>
