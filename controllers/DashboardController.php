<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController {

    public static function index(Router $router) {

        session_start();

        // Para proteger el dashboard (al invocar la ruta desde otro navegador en donde no se haya iniciado sesión)
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belonsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {

        session_start();

        // Proteger la ruta de crear-proyecto
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $proyecto = new Proyecto($_POST);

            // Validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {                 
                
                // Generar uns URL única
                $hash = md5( uniqid());
                $proyecto->url = $hash;                

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];             
                
                // Guardar el Proyecto
                $proyecto->guardar();                

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url );
                
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]);        
    }

    public static function proyecto(Router $router) {

        session_start();

        // Proteger la ruta de crear-proyecto
        isAuth();

        $token = $_GET['id'];
        if( !$token ) header('Location: /dashboard'); // en caso de no haber un token redirecciona a proyectos

        // Revisar que la persona que visita el proyecto, es quien lo creo.
        $proyecto = Proyecto::where('url', $token);

        if( $proyecto->propietarioId !== $_SESSION['id'] ) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router) {

        session_start();

        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
             $usuario->sincronizar($_POST);

             $alertas = $usuario->validar_pefil();

             if(empty($alertas)) {

                 // Validar que no exista el email que se esta actualizando
                 $existeUsuario = Usuario::where('email', $usuario->email);
                 
                 // existe el usuario y además el usuario es diferente al usuario autenticado
                 if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                     // Mensaje de error

                     Usuario::setAlerta('error', 'Email no válido, pertenece a otra cuenta');

                     // sacarlo de memoria
                     $alertas = $usuario->getAlertas();

                 } else {
                    // Guardar el registro

                    // guardar el usuario
                    $usuario->guardar(); // lo guarda en base de datos

                    Usuario::setAlerta('exito', 'Guardado Correctamente');

                    // sacarlo de memoria
                    $alertas = $usuario->getAlertas();
                    
                    // Asignar el nombre nuevo a la barra (se actualiza en memoria)
                    $_SESSION['nombre'] = $usuario->nombre;
                 }                 
             }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);        
    }

    public static function cambiar_password(Router $router) {

        session_start();

        isAuth();

        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
             // identificar al usuario que desea cambiar su password
             $usuario = Usuario::find($_SESSION['id']);

             // Sincronizar con los datos del usuario
             $usuario->sincronizar($_POST);

             $alertas = $usuario->nuevo_passwords();
             
             if(empty($alertas)) {
                 $resultado = $usuario->comprobar_password();

                 if($resultado) {                  

                    // El password nuevo se pasa hacia password
                    $usuario->password = $usuario->password_nuevo;

                     // Elimina propiedades no necesarias(password actual y nuevo) 
                     unset($usuario->password_actual);
                     unset($usuario->password_nuevo);

                     // Hashear el nuevo password
                     $usuario->hashPassword();

                     // Actualizar
                     $resultado = $usuario->guardar();

                     if($resultado) {
                         Usuario::setAlerta('exito', 'Pasword Guardado Correctamente');
                         $alertas = $usuario->getAlertas();
                     }

                 } else {
                     Usuario::setAlerta('error', 'Password Incorrecto');
                     $alertas = $usuario->getAlertas();
                 }
             }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }
}