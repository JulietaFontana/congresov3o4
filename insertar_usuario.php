    // Obtener el ID del nuevo usuario
    $id_usuario = $stmt->insert_id;

    // Asignar el rol 'user' por defecto (id_rol = 3 asumiendo el orden de inserción)
    $rol_stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
    $rol_user_id = 3; // ⚠️ Asegurate que este sea el id del rol 'user'
    $rol_stmt->bind_param("ii", $id_usuario, $rol_user_id);
    $rol_stmt->execute();
