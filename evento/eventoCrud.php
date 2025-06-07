<?php
    require '../conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();
        //INSERTAR
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $sql = "INSERT INTO evento (nombre, descripcion, fecha) VALUES (:nombre, :descripcion, :fecha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre', $_POST['nombre']);
            $stmt->bindValue(':descripcion', $_POST['descripcion']);
            $stmt->bindValue(':fecha', $_POST['fecha']);

            $stmt->execute();
            $idPost = $pdo->lastInsertId();

            if ($idPost) {
                header("HTTP/1.1 200 Ok");
                echo json_encode($idPost);
                exit;
            }
        }
        //LISTAR
        if($_SERVER['REQUEST_METHOD']=='GET'){
            $sql ="SELECT * FROM evento";
            $params=[];
            if (isset($_GET['id'])) {
                $sql .= " WHERE id=:id";
                $params[':id'] = $_GET['id'];
            }
            $stmt=$pdo->prepare($sql);
            $stmt->execute($params);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            header("HTTP/1.1 200 OK");
            echo json_encode($stmt->fetchAll());
            exit;
        }
        //ELIMINAR
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            if (!isset($_GET['id'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "ID requerido"]);
                exit;
            }
            $sql = "DELETE FROM evento WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $_GET['id']);
            $stmt->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode(["mensaje" => "Evento eliminado"]);
            exit;
        }

        //ACTUALIZAR    
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            if (!isset($_GET['id']) || !isset($_GET['nombre']) || !isset($_GET['descripcion']) || !isset($_GET['fecha'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Faltan datos"]);
                exit;
            }

            $sql = "UPDATE evento SET fecha=:fecha, nombre=:nombre, descripcion=:descripcion WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre', $_GET['nombre']);
            $stmt->bindValue(':fecha', $_GET['fecha']);
            $stmt->bindValue(':descripcion', $_GET['descripcion']);
            $stmt->bindValue(':id', $_GET['id']); // ← FALTABA ESTO
            $stmt->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode(["mensaje" => "Evento actualizado"]);
            exit;
        }

    // Si no coincide con ningún método de solicitud, devolver Bad Request
    header("HTTP/1.1 400 Bad Request");
?>