<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External functions and service definitions.
 *
 * Capability definitions
 *
 * @package report_vivo
 * @author Carlos Palacios <cjpm1983@gmail.com>
 * @copyright  Carlos Palacios 2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'report_get_user_by_custom_field' => array( //nombre de lafuncion delwebservice

        'classname'   => 'report_vivo_external', //clase que contiene lafuncion externa
        'methodname'  => 'get_user_from_cfield',
        'classpath'   => 'report/vivo/externallib.php',
        'description' => 'Retrieve the moodle user by providing the value of a defined custom profile field.',
        'type'        => 'read',//permisos en la base de datos(read , write)
        'ajax'        => true
        //'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)//OPCIONAL listar los servicios built-in por su shortname que loincorporan 
    ),
    'report_get_courses_by_teacher_id' => array( //nombre de lafuncion delwebservice

        'classname'   => 'report_vivo_external', //clase que contiene lafuncion externa, us� la mmisma
        'methodname'  => 'get_courses_by_teacher_id',
        'classpath'   => 'report/vivo/externallib.php',
        'description' => 'Retrieve the moodle courses by a given user id corresponding to the teacher.',
        'type'        => 'read',//permisos en la base de datos(read , write)
        'ajax'        => true
        //'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)//OPCIONAL listar los servicios built-in por su shortname que loincorporan 
    ),
    'report_get_courses_orphans' => array( //nombre de lafuncion delwebservice

        'classname'   => 'report_vivo_external', //clase que contiene lafuncion externa, us� la mmisma
        'methodname'  => 'get_courses_orphans',
        'classpath'   => 'report/vivo/externallib.php',
        'description' => 'Retrieve the moodle orphans courses.',
        'type'        => 'read',//permisos en la base de datos(read , write)
        'ajax'        => true
        //'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)//OPCIONAL listar los servicios built-in por su shortname que loincorporan 
    ),

);

$services = array(
    //en ingles para subirlo a moodle.org
   'VIVO External Service'  => array(
        'functions' => array (
            'report_get_user_by_custom_field',
            'report_get_courses_by_teacher_id',
            'report_get_courses_orphans',
			'core_course_get_courses',
			'core_course_get_courses_by_field',
			'core_enrol_get_enrolled_users',
			'core_enrol_get_users_courses',
			'core_user_get_users',
			'core_user_get_users_by_field',
        ),
        //'requiredcapability'=>'webservice/rest:use',//si se pone el usuario del servicio entonces necesitara de esa capacidad ej some/capability:especificar
        'enabled' => 1,
        'restrictedusers' => 0,//si se habilita entonces eladministrador debe vincular algun usuario a este servicio Veremosque es mejor
        'shortname' => 'vivo_external_service',
    ),
);