<?php
session_start(); 

$citas_reservadas = [];
$paciente_logueado = false;
$nombre_paciente = "Paciente";

if (isset($_SESSION['paciente_id'])) {
    $paciente_logueado = true;
    $paciente_id = $_SESSION['paciente_id'];
    
    $host= 'DESKTOP-59R7A1D\SQLEXPRESS01';
    $bd= 'CitasMedicas2';
    
    try {
        $conn = new PDO ("sqlsrv:Server=$host; DataBase=$bd;",null,null);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      
        $stmt_nombre = $conn->prepare("SELECT nombre_completo FROM Pacientes WHERE id_paciente = :id");
        $stmt_nombre->bindParam(':id', $paciente_id);
        $stmt_nombre->execute();
        $paciente_data = $stmt_nombre->fetch(PDO::FETCH_ASSOC);
        if ($paciente_data) {
            $nombre_paciente = explode(" ", $paciente_data['nombre_completo'])[0]; 
        }

        $stmt = $conn->prepare("
            SELECT 
                C.fecha, 
                C.hora, 
                C.especialidad_solicitada, 
                D.nombre AS nombre_doctor
            FROM Citas C
            JOIN Doctores D ON C.id_doctor = D.id_doctor
            WHERE C.id_paciente = :paciente_id
            ORDER BY C.fecha ASC, C.hora ASC
        ");
        $stmt->bindParam(':paciente_id', $paciente_id);
        $stmt->execute();
        $citas_reservadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        $citas_reservadas = [];
    }
}


if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Citas Médicas | Clínica Salud Plus</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif; 
            background-color: #e9ecef; 
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            margin-bottom: 20px; 
        }
        h1 {
            color: #1a73e8; 
            margin-bottom: 15px;
            font-weight: 600;
        }
        h2 { 
            color: #00b894;
            margin-bottom: 15px;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-block;
            background-color: #1a73e8;
            color: white;
            padding: 14px 30px;
            border-radius: 30px; 
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background-color: #0c5bb5;
            transform: translateY(-2px);
        }
        .logo-text {
            font-size: 0.9em;
            color: #888;
            margin-top: 10px;
            display: block;
        }
        .appointment-list { 
            list-style: none;
            padding: 0;
            text-align: left;
        }
        .appointment-list li {
            background-color: #f7f9fd;
            border-left: 5px solid #00b894;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-size: 1.05rem;
            color: #34495e;
        }
        .appointment-list li strong {
            color: #1a73e8;
        }
        .logout-link {
            display: block;
            text-align: right;
            margin-top: 15px;
            font-size: 0.9em;
            color: #d63031;
            text-decoration: none;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <?php if ($paciente_logueado): ?>
    <div class="container">
        <h2>Hola, <?php echo htmlspecialchars($nombre_paciente); ?> 👋 Tus Citas Reservadas</h2>
        <a href="index.php?logout=true" class="logout-link">Cerrar Sesión</a>
        
        <?php if (!empty($citas_reservadas)): ?>
            <ul class="appointment-list">
                <?php foreach ($citas_reservadas as $cita): ?>
                    <li>
                        Cita de **<?php echo htmlspecialchars($cita['especialidad_solicitada']); ?>** el 
                        <strong><?php echo date("d-m-Y", strtotime($cita['fecha'])); ?></strong> a las 
                        <strong><?php echo date("H:i", strtotime($cita['hora'])); ?></strong>. <br>
                        Con el Dr(a). **<?php echo htmlspecialchars($cita['nombre_doctor']); ?>**.
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="margin-bottom: 0;">🎉 ¡No tienes citas futuras reservadas! Es un buen momento para agendar una.</p>
        <?php endif; ?>
        
    </div>
    <?php endif; ?>
    
    <div class="container">
        <h1>🏥 Reserva tu Cita con Clínica Salud Plus</h1>
        <p>Una plataforma diseñada para cuidar tu tiempo y tu salud. Reserva citas con nuestros especialistas de manera fácil, rápida y completamente en línea.</p>
        <p>Nuestro sistema te permite visualizar la disponibilidad de nuestros doctores y agendar tu próxima consulta sin demoras. Tu bienestar es nuestra prioridad.</p>
        <a href="reservar_cita.php" class="btn">Reservar mi cita ahora</a>
        <span class="logo-text">Sistema de Gestión de Citas Médicas</span>
    </div>
</body>
</html>