<?php

/*
* ubicacion ddel plugin en menu admin
*
* @package report_vivo
* @author Carlos Palacios <cjpm1983@gmail.com>
* @copyright  Carlos Palacios 2020
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

$pluginname =  "VIVO";//get_string('pluginname'); //nombre de la clave, archivo de idioma

$ADMIN->add('reports', new admin_externalpage('reportvivo',
            $pluginname,
            new moodle_url('/report/vivo/index.php'),
            "report/vivo:view"));

//aqui iria la configuracion
$settings = null;   