<?php
    require '../conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();

    // INSCRIBIRSE A UN EVENTO
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = "INSERT INTO usuario_evento (usuario_id, evento_id) VALUES (:usuario_id, :evento_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $_POST['usuario_id']);
        $stmt->bindValue(':evento_id', $_POST['evento_id']);
        $stmt->execute();
        header("HTTP/1.1 200 OK");
        echo json_encode(['mensaje' => 'Inscripción exitosa']);
        exit;
    }

    // DARSE DE BAJA DE UN EVENTO
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $sql = "DELETE FROM usuario_evento WHERE usuario_id = :usuario_id AND evento_id = :evento_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $_GET['usuario_id']);
        $stmt->bindValue(':evento_id', $_GET['evento_id']);
        $stmt->execute();
        header("HTTP/1.1 200 OK");
        echo json_encode(['mensaje' => 'Te has dado de baja del evento']);
        exit;
    }

?>