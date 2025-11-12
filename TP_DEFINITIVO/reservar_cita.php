<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita - Clínica Salud Plus</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 550px;
        }
        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 5px;
            font-weight: 600;
        }
        h3 {
            color: #343a40;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #343a40;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.25);
            outline: none;
        }
        .form-group input[type="submit"] {
            background-color: #00b894;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 14px;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #009975;
            transform: translateY(-1px);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #1a73e8;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .alert-info {
            background-color: #e7f3ff;
            color: #1a73e8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #1a73e8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗓️ Agendar Nueva Cita</h1>
        <p class="alert-info">Selecciona tus datos, la **fecha limitada y el horario disponible** que más te convenga. Las citas tienen una duración de 30 minutos.</p>

        <form action="procesar_cita.php" method="POST">
            
            <h3>Datos del Paciente</h3>
            <div class="form-group">
                <label for="nombre_paciente">Nombre Completo:</label>
                <input type="text" id="nombre_paciente" name="nombre_paciente" required placeholder="Ej: Jennifer Orellana">
            </div>
            <div class="form-group">
                <label for="email_paciente">E-mail:</label>
                <input type="email" id="email_paciente" name="email_paciente" required placeholder="ejemplo@correo.com">
            </div>
            <div class="form-group">
                <label for="celular_paciente">Celular (WhatsApp):</label>
                <input type="tel" id="celular_paciente" name="celular_paciente" required placeholder="+54 9 XXXXXXXX">
            </div>

            <h3>Datos de la Cita</h3>
            <div class="form-group">
                <label for="especialidad">Especialidad Solicitada:</label>
                <select id="especialidad" name="especialidad" required>
                    <option value="" disabled selected>Seleccione una especialidad</option>
                    <option value="Cardiología">Cardiología</option>
                    <option value="Dermatología">Dermatología</option>
                    <option value="Pediatría">Pediatría</option>
                    <option value="Ginecología">Ginecología</option>
                    <option value="Oftalmología">Oftalmología</option>
                    </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha Disponible:</label>
                <select id="fecha" name="fecha" required>
                    <option value="" disabled selected>Seleccione una fecha (próximos 15 días)</option>
                    <option value="2025-11-15">Sábado, 15 de Noviembre</option>
                    <option value="2025-11-17">Lunes, 17 de Noviembre</option>
                    <option value="2025-11-18">Martes, 18 de Noviembre</option>
                    <option value="2025-11-20">Jueves, 20 de Noviembre</option>
                    <option value="2025-11-21">Viernes, 21 de Noviembre</option>
                    <option value="2025-11-24">Lunes, 24 de Noviembre</option>
                    <option value="2025-12-01">Lunes, 01 de Diciembre</option>
                    <option value="2025-12-02">Martes, 02 de Diciembre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="hora">Hora Disponible:</label>
                <select id="hora" name="hora" required>
                    <option value="" disabled selected>Seleccione un horario (9:00 a 17:00)</option>
                    <option value="09:00:00">09:00 AM</option>
                    <option value="09:30:00">09:30 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="10:30:00">10:30 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="11:30:00">11:30 AM</option>
                    <option value="14:00:00">02:00 PM</option>
                    <option value="14:30:00">02:30 PM</option>
                    <option value="15:00:00">03:00 PM</option>
                    <option value="15:30:00">03:30 PM</option>
                    <option value="16:00:00">04:00 PM</option>
                    <option value="16:30:00">04:30 PM</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" value="✅ Confirmar Reserva">
            </div>

        </form>
        <a href="index.php" class="back-link">← Volver a la página de inicio</a>
    </div>
</body>
</html>