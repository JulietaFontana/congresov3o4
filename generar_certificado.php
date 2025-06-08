<?php
session_start();
require 'db.php';
require('fpdf/fpdf.php'); // Asegurate de tener la carpeta fpdf con la librería FPDF

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Obtener email del usuario
$email_usuario = $_SESSION['email'];

// Buscar el id del usuario en la base
$sql_id = "SELECT id, nombre, apellido FROM usuarios WHERE email = ?";
$stmt_id = $conn->prepare($sql_id);
$stmt_id->bind_param("s", $email_usuario);
$stmt_id->execute();
$result_id = $stmt_id->get_result();
$row_id = $result_id->fetch_assoc();

$id_usuario = $row_id['id'] ?? null;
$nombre_usuario = $row_id['nombre'] . ' ' . $row_id['apellido'];

if (!$id_usuario) {
    echo "No se pudo encontrar el usuario.";
    exit();
}

// Datos del certificado
$nombre_evento = "Congreso Nacional 2026";
$fecha_emision = date("Y-m-d");

// Crear carpeta certificados si no existe
$dir_cert = 'certificados/';
if (!file_exists($dir_cert)) {
    mkdir($dir_cert, 0777, true);
}

// Generar PDF con FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 20, 'Certificado de Participacion', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, "Se certifica que:\n\n$nombre_usuario\n\nHa participado en el evento:\n$nombre_evento\n\nFecha de emision: $fecha_emision", 0, 'C');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Organizacion del Congreso Nacional 2026', 0, 1, 'C');

// Guardar el archivo
$archivo_certificado = $dir_cert . "certificado_" . $id_usuario . "_" . time() . ".pdf";
$pdf->Output('F', $archivo_certificado);

// Registrar en la base de datos
$sql_insert = "INSERT INTO certificados (id_usuario, nombre_evento, fecha_emision, archivo_certificado) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("isss", $id_usuario, $nombre_evento, $fecha_emision, $archivo_certificado);
$stmt_insert->execute();

// Redirigir a la página de certificados
header("Location: certificaciones.php");
exit();
?>
