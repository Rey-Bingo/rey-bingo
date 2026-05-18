<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Push extends BaseConfig
{
    // Mueve las claves a variables de entorno por seguridad
    public $vapidPublicKey = 'BHdM69ue9dsb7wxXHWJrea_2F45sWLUd2hx33imtzLotBrIeqReJWcSHW48hCkL7iU5XGns96G7G6ViIC6MC8nI';
    public $vapidPrivateKey = 'A7fqWe5ja-FawBl6wSwjgwsmmELSSJ0_YFuWsx50IEY';
    public $vapidSubject = 'mailto:marwinjsilva@gmail.com'; // Corregido el formato
}
