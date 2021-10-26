<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Filter form
 *
 * @author Carlos Palacios
 * @package report_vivo
 */
class draft_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'filterheader', "User ID for listing files");

        // foreach ($this->_customdata->get_filters() as $filter) {
        //     $filter->add_elements($mform);
        // }
        // $this->_customdata->mform_hook($mform);


        
        $atributes=array('size'=>'40');
        
        $mform->addElement('static','cadena1','',"USER ID");
        $mform->addElement('text','useridf');
        $mform->setType('useridf', PARAM_INT);


        $this->add_submit_buttons($mform);
    }

    /**
     * @param MoodleQuickForm $mform
     */
    function add_submit_buttons($mform) {
        $buttons = array();
        $buttons[] = &$mform->createElement('submit', 'submitbutton', 'LISTAR');
        //get_string('filter', 'report_vivo'));
        //  $buttons[] = &$mform->createElement('submit', 'resetbutton', get_string('reset', 'report_vivo'));
        $mform->addGroup($buttons, 'buttons', '', array(' '), false);

        $mform->registerNoSubmitButton('reset');
    }
}
