<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// Validation language settings
return [
    // Core Messages
    'noRuleSets'      => 'No se han establecido reglas en la configuración de validación.',
    'ruleNotFound'    => '{0} no es una regla de validación válida.',
    'groupNotFound'   => '{0} no es un grupo de reglas de validación.',
    'groupNotArray'   => '{0} el grupo de validación debe ser un array.',
    'invalidTemplate' => '{0} no es un modelo de validación válido.',

    // Rule Messages
    'alpha'                 => '{field} solo puede contener caracteres alfabéticos.',
    'alpha_dash'            => '{field} solo puede contener caracteres alfanuméricos, subrayados, y guiones.',
    'alpha_numeric'         => '{field} solo puede contener caracteres alfanuméricos.',
    'alpha_numeric_punct'   => '{field} solo puede contener caracteres alfanuméricos, espacios, y los caracteres ~ ! # $ % & * - _ + = | : . .',
    'alpha_numeric_space'   => '{field} solo puede contener caracteres alfanuméricos y espacios.',
    'alpha_space'           => '{field} solo puede contener caracteres alfabéticos y espacios.',
    'decimal'               => '{field} debe contener un número decimal.',
    'differs'               => '{field} debe diferir del {param}.',
    'equals'                => '{field} debe ser exactamente: {param}.',
    'exact_length'          => '{field} debe tener exactamente {param} caractéres de longitud.',
    'field_exists'          => '{field} debe existir.',
    'greater_than'          => '{field} debe ser mayor que {param}.',
    'greater_than_equal_to' => '{field} debe ser mayor o igual a {param}.',
    'hex'                   => '{field} solo puede contener caracteres hexadecimales.',
    'in_list'               => '{field} debe ser uno de: {param}.',
    'integer'               => '{field} debe contener un entero.',
    'is_natural'            => '{field} debe contener solo dígitos.',
    'is_natural_no_zero'    => '{field} debe solo contener dígitos y ser mayor que cero.',
    'is_not_unique'         => '{field} debe contener un valor previamente existente en la base de datos.',
    'is_unique'             => '{field} ya esta registrado.',
    'less_than'             => '{field} debe ser menor que {param}.',
    'less_than_equal_to'    => '{field} debe ser menor o igual a {param}.',
    'matches'               => '{field} no coincide con el {param}.',
    'max_length'            => '{field} no pude exceder los {param} caracteres de longitud.',
    'min_length'            => '{field} debe tener al menos {param} caracteres de longitud.',
    'not_equals'            => '{field} no puede ser: {param}.',
    'not_in_list'           => '{field} no debe ser uno de: {param}.',
    'numeric'               => '{field} debe contener solo números.',
    'regex_match'           => '{field} no está en el formato correcto.',
    'required'              => '{field} es obligatorio.',
    'required_with'         => '{field} es obligatorio cuando {param} está presente.',
    'required_without'      => '{field} es obligatorio cuando {param} no está presente.',
    'string'                => '{field} debe ser una cadena válida.',
    'timezone'              => '{field} debe ser una zona horaria válida.',
    'valid_base64'          => '{field} debe ser una cadena base64 válida.',
    'valid_email'           => '{field} debe contener una dirección de email válida.',
    'valid_emails'          => '{field} debe contener todas las direcciones de email válidas.',
    'valid_ip'              => '{field} debe contener una IP válida.',
    'valid_url'             => '{field} debe contener una URL válida.',
    'valid_url_strict'      => '{field} debe contener una URL válida.',
    'valid_date'            => '{field} debe contener una fecha válida.',
    'valid_json'            => '{field} debe contener un json válido.', // 'The {field} field must contain a valid json.',

    // Credit Cards
    'valid_cc_num' => '{field} no parece ser un número de tarjeta de crédito válida.',

    // Files
    'uploaded' => '{field} no es un de subida de archivo válido.',
    'max_size' => '{field} es demasiado grande para un archivo.',
    'is_image' => '{field} no es válido, subido archivo de imagen.',
    'mime_in'  => '{field} no tiene un tipo válido de mime.',
    'ext_in'   => '{field} no tiene una extensión de archivo válida.',
    'max_dims' => '{field} no es una imagen o tiene demasiado alto o ancho.',
];
