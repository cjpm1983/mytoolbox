<?php


/**
 * Capability definitions
 *
 * @package report_vivo
 * @author Carlos Palacios <cjpm1983@gmail.com>
 * @copyright  Carlos Palacios 2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'report/vivo:view' => array(
        'riskbitmask'  => RISK_MANAGETRUST | RISK_CONFIG | RISK_XSS | RISK_PERSONAL | RISK_SPAM | RISK_DATALOSS,
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,//CONTEXT_SYSTEM
        'archetypes'   => array(
            'manager'   =>  'CAP_ALLOW',
        )
    ),
);
