<?php

namespace Model;

class Usuario extends ActiveRecord { // Heredando de ActiveRecord
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null; // ?? sino esta presente manda null
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        
    }

    // Validar el login de usuarios
    public function validarLogin() {

        if(!$this->email) {
             self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
          self::$alertas['error'][] = 'Email no válido';
        }
          
        if(!$this->password) {
             self::$alertas['error'][] = 'El Password no puede ir vacío';
        } 

        return self::$alertas;
    }

    // Validación para cuentas nuevas
    public function validarNuevaCuenta() {
       if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Usuario es Obligatorio';
       }

       if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
       }
       
       if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacío';
       }

       if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
       }

       if($this->password !== $this->password2) { // pasword es diferente a pasword2
            self::$alertas['error'][] = 'Los password son diferentes';
       }

       return self::$alertas;
    }

    // Valida un Email
    public function validarEmail() {
        if(!$this->email) {
           self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
           self::$alertas['error'][] = 'Email no válido';
        }

        return self::$alertas;
    }

    // Valida el Password
    public function validarPassword() {

        if(!$this->password) {
             self::$alertas['error'][] = 'El Password no puede ir vacío';
        }

        if(strlen($this->password) < 6) {
             self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validar_pefil() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        return self::$alertas;
    }

    public function nuevo_passwords() : array { // $this no se puede usar si la función es estatica solo en publicas 
        if( !$this->password_actual ) {
            self::$alertas['error'][] = 'EL password actual no puede ir vacío';
        }

        if( !$this->password_nuevo ) {
            self::$alertas['error'][] = 'EL password nuevo no puede ir vacío';
        }

        if( strlen($this->password_nuevo) < 6 ) {
            self::$alertas['error'][] = 'EL password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Comprobar Password
    public function comprobar_password() : bool { // bool: retorna True o False
         return password_verify($this->password_actual, $this->password);
    }

    // Hashea el Password
    public function hashPassword() : void { // void (vacío) porque no retorna nada
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar un Token
    public function crearToken() : void {
        $this->token = uniqid();

        // tambien se puede usuar de esta forma si se desea una cadena más larga (32 caracteres) de caracteres para el token
        // $this->token = md5( uniqid() );
    }
}