<?php
        require '../conexion.php';
        $conexion = new Conexion();
        $pdo = $conexion->obtenerConexion();


        //ENVIAR DUTS DE UN ESTUDIANTE A OTRO 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Verificar si los parámetros obligatorios están presentes
            if (!isset($_POST['id_origen'], $_POST['id_destino'], $_POST['cantidad'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan uno o mas parametros requeridos: id_origen, id_destino, cantidad']);
                exit;
            }

            // Verificar que los campos no estén vacíos
            if (empty($_POST['id_origen']) || empty($_POST['id_destino']) || empty($_POST['cantidad'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Los parametros no pueden estar vacios']);
                exit;
            }

            
            $origen = $_POST['id_origen'];
            $destino = $_POST['id_destino'];
            $cantidad = floatval($_POST['cantidad']);
            
            //Verifica que la cantidad sea mayor que cero
            if ($cantidad <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'La cantidad debe ser mayor que cero']);
                exit;
            }

            // 1. Obtener el saldo del usuario origen
            $stmt = $pdo->prepare("SELECT duts_actual FROM usuario WHERE id = :id");
            $stmt->bindValue(':id', $origen);
            $stmt->execute();
            $origen_saldo = $stmt->fetchColumn();

            if ($origen_saldo === false) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario origen no encontrado']);
                exit;
            }

            if ($origen_saldo < $cantidad) {
                http_response_code(400);
                echo json_encode(['error' => 'Saldo insuficiente']);
                exit;
            }

            // 2. Iniciar una transacción para asegurar que ambas operaciones se realicen juntas
            $pdo->beginTransaction();

            try {
                // 3. Restar del origen
                $stmt = $pdo->prepare("UPDATE usuario SET duts_actual = duts_actual - :cantidad WHERE id = :id");
                $stmt->execute([':cantidad' => $cantidad, ':id' => $origen]);

                // 4. Sumar al destino
                $stmt = $pdo->prepare("UPDATE usuario SET duts_actual = duts_actual + :cantidad WHERE id = :id");
                $stmt->execute([':cantidad' => $cantidad, ':id' => $destino]);

                // 5. (Opcional) Registrar la transacción en una tabla `duts`
                // Registrar la transacción en la tabla `transacciones`
                $stmt = $pdo->prepare("INSERT INTO duts_transacciones (id_origen, id_destino, cantidad, fecha, tipo)
                    VALUES (:origen, :destino, :cantidad, NOW(), 'transaccion')");

                $stmt->execute([
                    ':origen' => $origen,
                    ':destino' => $destino,
                    ':cantidad' => $cantidad
                ]);


                $pdo->commit();

                echo json_encode(['mensaje' => 'Transferencia exitosa']);
            } catch (Exception $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['error' => 'Error en la transferencia', 'detalle' => $e->getMessage()]);
            }
        }

        //VER PROMEDIO y TOTAL DE DUTS EN INTERVALOS DE TIEMPO 
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id_estudiante'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Falta el parametro id_estudiante']);
                exit;
            }

            $id = $_GET['id_estudiante'];

            $sql = "
                SELECT
                --promedio
                    ROUND(COALESCE(AVG(CASE WHEN fecha >= NOW() - INTERVAL 7 DAY THEN cantidad END),0), 2) AS promedio_semana,
                    ROUND(COALESCE(AVG(CASE WHEN fecha >= NOW() - INTERVAL 1 MONTH THEN cantidad END),0), 2) AS promedio_mes,
                    ROUND(COALESCE(AVG(CASE WHEN fecha >= NOW() - INTERVAL 6 MONTH THEN cantidad END),0), 2) AS promedio_semestre,
                    ROUND(COALESCE(AVG(CASE WHEN fecha >= NOW() - INTERVAL 1 YEAR THEN cantidad END),0), 2) AS promedio_anio,
                    ROUND(COALESCE(AVG(cantidad),0), 2) AS promedio_total,
                -- Totales
                    COALESCE(SUM(CASE WHEN fecha >= NOW() - INTERVAL 7 DAY THEN cantidad END),0) AS total_semana,
                    COALESCE(SUM(CASE WHEN fecha >= NOW() - INTERVAL 1 MONTH THEN cantidad END),0) AS total_mes,
                    COALESCE(SUM(CASE WHEN fecha >= NOW() - INTERVAL 6 MONTH THEN cantidad END),0) AS total_semestre,
                    COALESCE(SUM(CASE WHEN fecha >= NOW() - INTERVAL 1 YEAR THEN cantidad END),0) AS total_anio,
                    COALESCE(SUM(cantidad),0) AS total_general
                FROM duts_transacciones
                WHERE id_destino = :id AND tipo = 'entrada'
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode($resultados);
            http_response_code(200);
            exit;
        }
// Si no coincide con ningún método de solicitud, devolver Bad Request
    header("HTTP/1.1 400 Bad Request");
?>
