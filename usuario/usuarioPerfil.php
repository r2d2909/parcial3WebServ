<?php

    require '../conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();
    
    
    //Ver perfil 
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['id_estudiante'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta el parametro id_estudiante']);
            exit;
        }

        $id = $_GET['id_estudiante'];

        $sql = "SELECT nombres, apellidos, correo, ciudad, pais, descripcion, intereses, programa, semestre, username, duts_actual
                FROM usuario
                WHERE id = :id AND rol = 'estudiante'";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($perfil) {
            echo json_encode($perfil);
            http_response_code(200);
        } else {
            echo json_encode(['error' => 'Estudiante no encontrado']);
            http_response_code(404);
        }
        exit;
    }







?>