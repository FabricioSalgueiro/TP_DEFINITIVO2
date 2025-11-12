<?php
session_start(); 

$host= 'DESKTOP-59R7A1D\SQLEXPRESS01';
$bd= 'CitasMedicas2';
try {
   
    $conn = new PDO ("sqlsrv:Server=$host; DataBase=$bd;",null,null);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    
    die("Error de conexión a la base de datos: " . $e->getMessage());
}


$style_output = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Resultado de Reserva</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
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
            max-width: 600px;
            width: 100%;
        }
        h1 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .success h1 {
            color: #00b894; /* Verde para éxito */
        }
        .error h1, .unauthorized h1 {
            color: #d63031; /* Rojo para error */
        }
        .btn {
            display: inline-block;
            background-color: #1a73e8;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0c5bb5;
        }
        /* NUEVO ESTILO PARA LA NOTA DE HISTORIAL */
        .alert-info-historial {
            background-color: #f7f9fd; 
            color: #1a73e8;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            margin-bottom: 15px !important; 
            border: 1px solid #e0e6f0;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class='container ";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre_paciente = $_POST['nombre_paciente'];
    $email_paciente = $_POST['email_paciente'];
    $celular_paciente = $_POST['celular_paciente'];
    $especialidad_solicitada = $_POST['especialidad'];
    $fecha_cita = $_POST['fecha'];
    $hora_cita = $_POST['hora'];

    try {
        
        
        $stmt = $conn->prepare("SELECT id_paciente FROM Pacientes WHERE email = :email");
        $stmt->bindParam(':email', $email_paciente);
        $stmt->execute();
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

        $paciente_id = null;

        if ($paciente) {
            
            $paciente_id = $paciente['id_paciente'];
        } else {
           
            $stmt = $conn->prepare("INSERT INTO Pacientes (nombre_completo, email, celular) VALUES (:nombre, :email, :celular)");
            $stmt->bindParam(':nombre', $nombre_paciente);
            $stmt->bindParam(':email', $email_paciente);
            $stmt->bindParam(':celular', $celular_paciente);
            $stmt->execute();
            
           
            $paciente_id = $conn->lastInsertId();
        }
        
        $_SESSION['paciente_id'] = $paciente_id;
        

        $stmt_doctor = $conn->prepare("
            SELECT TOP 1 D.id_doctor, D.nombre 
            FROM Doctores D
            WHERE D.especialidad = :especialidad 
            AND D.id_doctor NOT IN (
                SELECT C.id_doctor
                FROM Citas C
                WHERE C.fecha = :fecha AND C.hora = :hora
            )
            ORDER BY D.id_doctor ASC
        ");
        $stmt_doctor->bindParam(':especialidad', $especialidad_solicitada);
        $stmt_doctor->bindParam(':fecha', $fecha_cita);
        $stmt_doctor->bindParam(':hora', $hora_cita);
        $stmt_doctor->execute();
        $doctor = $stmt_doctor->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            
            $doctor_id = $doctor['id_doctor'];
            $nombre_doctor = $doctor['nombre'];

            $stmt = $conn->prepare("INSERT INTO Citas (id_paciente, id_doctor, fecha, hora, especialidad_solicitada) VALUES (:paciente_id, :doctor_id, :fecha, :hora, :especialidad)");
            $stmt->bindParam(':paciente_id', $paciente_id);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':fecha', $fecha_cita);
            $stmt->bindParam(':hora', $hora_cita);
            $stmt->bindParam(':especialidad', $especialidad_solicitada);
            $stmt->execute();

            $stmt_historial = $conn->prepare("SELECT TOP 1 diagnostico, fecha_registro FROM HistorialesMedicos WHERE id_paciente = :paciente_id ORDER BY fecha_registro DESC");
            $stmt_historial->bindParam(':paciente_id', $paciente_id);
            $stmt_historial->execute();
            $historial_reciente = $stmt_historial->fetch(PDO::FETCH_ASSOC);
            
            $info_historial = "";
            if ($historial_reciente) {
                $fecha_historial = date("d/m/Y", strtotime($historial_reciente['fecha_registro']));

                $diagnostico_base = $historial_reciente['diagnostico'] ?? 'Sin diagnóstico registrado';
                $diagnostico_corto = substr($diagnostico_base, 0, 50) . (strlen($diagnostico_base) > 50 ? '...' : '');
                $info_historial = "<p class='alert-info-historial'>📂 Historial Médico Encontrado: Su último registro del {$fecha_historial} (Diagnóstico: *{$diagnostico_corto}*) está disponible para el doctor.</p>";
            } else {
                $info_historial = "<p class='alert-info-historial'>⭐ **Primera Cita:** Es tu primera consulta. El Dr(a). abrirá tu nuevo historial médico.</p>";
            }


            echo $style_output . "success'>
                <h1>🎉 ¡Cita Confirmada con Éxito!</h1>
                <p>Tu reserva para <strong>{$especialidad_solicitada}</strong> el <strong>" . date("d-m-Y", strtotime($fecha_cita)) . "</strong> a las <strong>" . date("H:i", strtotime($hora_cita)) . "</strong> ha sido agendada.</p>
                <p>Serás atendido por el {$especialidad_solicitada} Dr(a). {$nombre_doctor}.</p>
                
                {$info_historial} <p>Recibirás un correo de confirmación en las próximas horas. ¡Gracias por preferir Clínica Salud Plus!</p>
                <a href='index.php' class='btn'>Volver al Inicio</a>
            </div>
        </body>
        </html>";

        } else {

            echo $style_output . "error'>
                <h1>⚠️ Horario No Disponible</h1>
                <p>Lo sentimos, todos nuestros doctores de <strong>{$especialidad_solicitada}</strong> ya tienen una cita agendada para la fecha <strong>" . date("d-m-Y", strtotime($fecha_cita)) . "</strong> a las <strong>" . date("H:i", strtotime($hora_cita)) . "</strong>.</p>
                <p>Por favor, intenta agendar en otro horario o fecha.</p>
                <a href='reservar_cita.php' class='btn'>Intentar de Nuevo</a>
            </div>
        </body>
        </html>";
        }

    } catch (PDOException $e) {
       
        die("Error al procesar la cita: " . $e->getMessage());
    }

} else {
   
    echo $style_output . "unauthorized'>
        <h1>🛑 Acceso No Autorizado</h1>
        <p>Esta página solo puede ser accedida al enviar el formulario de citas.</p>
        <p>Por favor, usa el botón de abajo para reservar una cita.</p>
        <a href='index.php' class='btn'>Ir a Reservar Cita</a>
    </div>
</body>
</html>";
}
?>