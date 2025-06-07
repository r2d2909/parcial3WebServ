<?php
    require '../conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();


    
    //INSERTAR
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (empty($_POST['nombres']) || empty($_POST['username']) || empty($_POST['password'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Campos requeridos faltantes"]);
                exit;
        }
        $sql = "INSERT INTO usuario (nombres, apellidos, correo, ciudad, pais, descripcion, intereses, programa, semestre, username, password, rol) VALUES (:nombres, :apellidos, :correo, :ciudad, :pais, :descripcion, :intereses, :programa, :semestre, :username, :password, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nombres', $_POST['nombres']);
        $stmt->bindValue(':apellidos', $_POST['apellidos']);
        $stmt->bindValue(':ciudad', $_POST['ciudad']);
        $stmt->bindValue(':pais', $_POST['pais']);
        $stmt->bindValue(':descripcion', $_POST['descripcion']);
        $stmt->bindValue(':intereses', $_POST['intereses']);
        $stmt->bindValue(':programa', $_POST['programa']);
        $stmt->bindValue(':semestre', $_POST['semestre']);
        $stmt->bindValue(':username', $_POST['username']);
        $stmt->bindValue(':password', $_POST['password']);
        $stmt->bindValue(':rol', $_POST['rol']);
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
        $sql ="SELECT * FROM usuario";
        $params=[];
        if(isset($_GET['id'])){
                $sql .=" WHERE id=:id";
                $params[':id']=$_GET['id'];       
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
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "ID inválido"]);
            exit;
        }
        $sql = "DELETE FROM usuario WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            header("HTTP/1.1 200 OK");
            echo json_encode(["mensaje" => "Usuario eliminado correctamente"]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
        exit;
    }
    //ACTUALIZAR    
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $_PUT);
        if (!isset($_PUT['id'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Falta el ID del usuario."]);
            exit;
        }
        $sql = "UPDATE usuario SET nombres = :nombres, apellidos = :apellidos, correo = :correo, ciudad = :ciudad, pais = :pais, descripcion = :descripcion, intereses = :intereses, programa = :programa, semestre = :semestre, username = :username, password = :password, rol = :rol WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nombres', $_PUT['nombres']);
        $stmt->bindValue(':apellidos', $_PUT['apellidos']);
        $stmt->bindValue(':correo', $_PUT['correo']);
        $stmt->bindValue(':ciudad', $_PUT['ciudad']);
        $stmt->bindValue(':pais', $_PUT['pais']);
        $stmt->bindValue(':descripcion', $_PUT['descripcion']);
        $stmt->bindValue(':intereses', $_PUT['intereses']);
        $stmt->bindValue(':programa', $_PUT['programa']);
        $stmt->bindValue(':semestre', $_PUT['semestre']);
        $stmt->bindValue(':username', $_PUT['username']);
        $stmt->bindValue(':password', $_PUT['password']);
        $stmt->bindValue(':rol', $_PUT['rol']);
        $stmt->bindValue(':id', $_PUT['id']);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            header("HTTP/1.1 200 OK");
            echo json_encode(["mensaje" => "Usuario actualizado"]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["error" => "No se encontró el usuario o los datos son iguales."]);
        }
        exit;
    }
    // Si no coincide con ningún método de solicitud, devolver Bad Request
    header("HTTP/1.1 400 Bad Request");
?>
