<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Filter form
 *
 * @author Carlos Palacios
 * @package report_vivo
 */
class vivo_form3 extends moodleform {

    function definition() {
        $mform3 =& $this->_form;

        $mform3->addElement('hidden','listarhuerfanos',1);
        $mform3->setType('listarhuerfanos', PARAM_INTEGER);

        $this->add_submit_buttons($mform3);
    }

    /**
     * @param MoodleQuickForm $mform3
     */
    function add_submit_buttons($mform3) {
        $buttons = array();
        $buttons[] = &$mform3->createElement('submit', 'submitbutton', get_string('cargarcursos','report_vivo'));
        //get_string('filter', 'report_vivo'));
        //  $buttons[] = &$mform3->createElement('submit', 'resetbutton', get_string('reset', 'report_vivo'));
        $mform3->addGroup($buttons, 'buttons', '', array(' '), false);

        $mform3->registerNoSubmitButton('reset');
    }
}
