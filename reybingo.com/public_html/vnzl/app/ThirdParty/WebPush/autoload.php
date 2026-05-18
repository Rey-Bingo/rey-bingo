<?php

// Autoloader personalizado para WebPush
spl_autoload_register(function ($class) {
    // Solo manejar clases del namespace Minishlink\WebPush
    if (strpos($class, 'Minishlink\\WebPush\\') !== 0) {
        return;
    }
    
    // Convertir namespace a ruta de archivo
    $file = str_replace('Minishlink\\WebPush\\', '', $class);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    $file = APPPATH . 'ThirdParty/WebPush/src/' . $file . '.php';
    
    // Cargar el archivo si existe
    if (file_exists($file)) {
        require $file;
    }
});
