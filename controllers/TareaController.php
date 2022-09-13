<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {

    // Con Fetch API no se requiere router, todo el CRUD se hace a traves de una API en el servidor
    public static function index() {

        $proyectoId = $_GET['id'];

        if( !$proyectoId ) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoId);

        session_start();

        if( !$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belonsTo('proyectoId', $proyecto->id);

        echo json_encode( ['tareas' => $tareas]);       
    }

    public static function crear() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            session_start();

            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url', $proyectoId);

            if( !$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar la tarea'
                ];
                echo json_encode($respuesta); // convertimos el array a formato json para que JavaScript lo pueda leer

                return;                
            }
            
            // Todo bien instanciar y crear la tarea
            $tarea = new Tarea($_POST);

            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea->guardar();

            $respuesta = [
                'tipo'    => 'exito',
                'id'      => $resultado['id'],
                'mensaje' => 'Tarea Creada Correctamente',
                'proyectoId' => $proyecto->id
            ];

            echo json_encode($respuesta); // para comunicarse con JavaScript tiene que ser json

        }
    }

    public static function actualizar() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            session_start();

            if( !$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta); // convertimos el array a formato json para que JavaScript lo pueda leer

                return;                
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea->guardar();

            if($resultado) {
                $respuesta = [
                    'tipo'       => 'exito',
                    'id'         => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje'    => 'Actualizado correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }            
        }
    }

    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            session_start();

            if( !$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta); // convertimos el array a formato json para que JavaScript lo pueda leer
                return;                
            }

            $tarea = new Tarea($_POST);

            $resultado = $tarea->eliminar();

            $resultado = [
                'resultado' => $resultado,
                'mensaje' => 'Eliminado Correctamente',
                'tipo' => 'exito'
            ];

            echo json_encode($resultado);
        }
    }
}