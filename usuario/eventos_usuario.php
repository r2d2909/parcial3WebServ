<?php
    require '../conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();

    // INSCRIBIRSE A UN EVENTO
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

         if (!isset($_POST['usuario_id'], $_POST['evento_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan uno o mas parametros requeridos']);
                exit;
            }
            // Verificar que los campos no estén vacíos
            if (empty($_POST['usuario_id']) || empty($_POST['evento_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Los parametros no pueden estar vacios']);
                exit;
            }


        $sql = "INSERT INTO usuario_evento (usuario_id, evento_id) VALUES (:usuario_id, :evento_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $_POST['usuario_id']);
        $stmt->bindValue(':evento_id', $_POST['evento_id']);
        $stmt->execute();
        header("HTTP/1.1 200 OK");
        echo json_encode(['mensaje' => 'Inscripcion exitosa']);
        exit;
    }

    // DARSE DE BAJA DE UN EVENTO
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    if (!isset($_GET['usuario_id'], $_GET['evento_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan uno o más parametros requeridos']);
        exit;
    }

    $usuario_id = (int) $_GET['usuario_id'];
    $evento_id = (int) $_GET['evento_id'];

    if ($usuario_id <= 0 || $evento_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Los parametros deben ser validos y mayores a cero']);
        exit;
    }

    $sql = "DELETE FROM usuario_evento WHERE usuario_id = :usuario_id AND evento_id = :evento_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindValue(':evento_id', $evento_id, PDO::PARAM_INT);
    $stmt->execute();

    header("HTTP/1.1 200 OK");
    echo json_encode(['mensaje' => 'Te has dado de baja del evento']);
    exit;
}


?>