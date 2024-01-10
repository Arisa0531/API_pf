<?php session_start();
    // Incluir las conexiones a la base de datos
    include_once 'connection/cnx_bd.php';
    // Conexión a base de datos de catalogos (bd_telegram)
    $cnx = new Conexion();$cnx = $cnx->Conectar();
    // Evaluar si existe uso de la api
    if(isset($_POST['regi'])){
        // Obtener los datos
        $data = $_POST['regi'];
        // Extraer la informacion
        $user = $data[0];
        $pass = password_hash($data[1],PASSWORD_DEFAULT);
        // Buscar usuario repetido
        $repe = current($cnx->query("SELECT COUNT(id) FROM td_usuarios WHERE usuario = '$user'")->fetch());
        // Evaluar si hay usuario repetido
        if($repe == 0){
            // Obtener el total de registros
            $maxi = current($cnx->query("SELECT COUNT(id) FROM td_usuarios")->fetch()) + 1;
            // Generar el query
            $query = "INSERT INTO td_usuarios (id,usuario,contrasena,articulos) VALUES ('$maxi','$user','$pass','[]');";
            // Ejecutar el query
            $query = $cnx->prepare("$query");
            if($query->execute()){
                // Generar variable de sesion
                $_SESSION['login'] = $maxi;
                // Retornamos que se genero el registro
                echo 1;
            } else {echo 'Lo sentimos, ocurrio un error';}
        } else {echo 'El nombre de usuario ya esta en uso';}
    } else if(isset($_POST['sess'])){
        // Obtener los datos
        $data = $_POST['sess'];
        // Extraer la informacion
        $user = $data[0];
        $pass = $data[1];
        // Buscar datos
        $busc = $cnx->prepare("SELECT * FROM td_usuarios WHERE usuario = '$user'");
        $busc->execute();
        // Evaluar si existe el usuario
        if($busc->rowCount() > 0){
            $info = $busc->fetch(PDO::FETCH_ASSOC);
            // Extraer su informacion
            $id = $info['id'];
            $cont = $info['contrasena'];
            // Evaluar contraseña
            if(password_verify($pass,$cont)){
                // Creamos la variable de sesion
                $_SESSION['login'] = $id;
                // Retornamos el id del usuario
                echo 1;
            } else {echo 'La contraseña es incorrecta';}
        } else {echo 'No cuenta con su registro';}
    } else if(isset($_POST['cerr'])){
        // Eliminar la varible de sesion
        session_destroy();
        echo 1;
    } else if(isset($_POST['carr'])){
        // Obtentener el arreglo del carrito
        if($_POST['carr'] == -1){$carr = json_encode([]);}
        else{$carr = json_encode($_POST['carr']);}
        // Obtemer el id del usuario
        $user = $_SESSION['login'];
        // Generar el query
        $query = "UPDATE td_usuarios SET articulos = '$carr' WHERE id = '$user';";
        // Ejecutamos la consulta
        $query = $cnx->prepare("$query");
        if($query->execute()){echo 1;}
    } else {echo 0;}
?>