<?php
        require '../conexion.php';
        $conexion = new Conexion();
        $pdo = $conexion->obtenerConexion();

        //iniciar sesion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Verificar si los parámetros obligatorios están presentes
            if (!isset($_POST['username'], $_POST['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan uno o mas parametros requeridos: username o password']);
                exit;
            }
            // Verificar que los campos no estén vacíos
            if (empty($_POST['username']) || empty($_POST['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Los parametros no pueden estar vacios']);
                exit;
            }
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sql = "SELECT * FROM usuario WHERE username = :username AND password = :password AND rol = 'estudiante'";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password); 
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $token = bin2hex(random_bytes(16)); 
                $update = $pdo->prepare("UPDATE usuario SET token = :token WHERE id = :id");
                $update->bindValue(':token', $token);
                $update->bindValue(':id', $usuario['id']);
                $update->execute();

                echo json_encode([
                    'token' => $token,
                    'id' => $usuario['id']
                ]);
                http_response_code(200);
            } else {
                echo json_encode(['error' => 'Credenciales invalidas']);
                http_response_code(401);
            }
            exit;
        }




?>