<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos (cambia los valores según tu configuración)
$servername = "192.168.18.20";
$username = "lsdbp";
$password = "Coope2022";
$dbname = "test";

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];

// Consulta para obtener los datos del usuario desde la base de datos
$sql = "SELECT username FROM usuarios WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Datos del usuario obtenidos de la base de datos
    $nombreReceptor = $row["username"];
    $numeroCedulaReceptor = $row["cedula"];
    $numTelefonoReceptor = $row["telefono"];
    $correoElectronicoReceptor = $row["correo"];
} else {
    echo "No se encontraron datos del usuario en la base de datos.";
    exit();
}

// Construir el arreglo con los datos de la factura
$facturaData = array(
    "FacturaElectronica" => array(
        "CodigoVenta" => "123456789456",
        "FechaEmision" => "2023-07-17T21:48:00",
        "CedulaEmisor" => "123456789",
        "Receptor" => array(
            "NombreReceptor" => $nombreReceptor,
            "TipoIdentificacionReceptor" => "01",
            "NumeroCedulaReceptor" => 12334122,
            "NumTelefonoReceptor" => 312312313,
            "CorreoElectronicoReceptor" => "donpepe@gmail.com"
        ),
        "MedioPago" => "02",
        "DetalleServicio" => array(
            "LineaDetalle" => array(
                array(
                    "NumeroLinea" => 1,
                    "Codigo" => "AKA87",
                    "Cantidad" => 2,
                    "Detalle" => "Fichas VIP",
                    "PrecioUnitario" => 2400.00000,
                    "MontoTotal" => 4800.00000,
                    "Descuento" => array(
                        "MontoDescuento" => 500.00000,
                        "NaturalezaDescuento" => "Porque me cae bien"
                    ),
                    "SubTotal" => 4300.00000,
                    "Impuesto" => array(
                        "Codigo" => "01",
                        "CodigoTarifa" => "08",
                        "Tarifa" => 13.00,
                        "Monto" => 546.00000
                    ),
                    "ImpuestoNeto" => 546.00000,
                    "MontoTotalLinea" => 4846.00000
                )
            )
        ),
        "ResumenFactura" => array(
            "CodigoTipoMoneda" => array(
                "CodigoMoneda" => "CRC",
                "TipoCambio" => 1.00000
            ),
            "TotalVenta" => 4800.00000,
            "TotalDescuentos" => 500.00000,
            "TotalVentaNeta" => 4800.00000,
            "TotalImpuesto" => 546.00000,
            "TotalComprobante" => 4800.00000
        )
    )
);

// Convertir el arreglo a una cadena JSON
$jsonDataString = json_encode($facturaData, JSON_PRETTY_PRINT);

$apiUrl = "https://jellyfish-app-s4g2w.ondigitalocean.app/factura";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataString); // Enviar la cadena JSON en el cuerpo de la solicitud
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonDataString),
    'Accept: application/xml' // Solicitar respuesta en formato XML
));

$response = curl_exec($ch);
curl_close($ch);

// Generar un archivo XML y ofrecerlo para descarga
$xmlFilename = "respuesta_api.xml";
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="' . $xmlFilename . '"');
echo $response;
exit; // Detener la ejecución del resto de la página

$conn->close();
?>
