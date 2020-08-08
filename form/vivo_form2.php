<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Filter form
 *
 * @author Carlos Palacios
 * @package report_vivo
 */
class vivo_form2 extends moodleform {

    function definition() {
        $mform2 =& $this->_form;

        $mform2->addElement('header', 'filterheader', get_string('form2header','report_vivo'));

        // foreach ($this->_customdata->get_filters() as $filter) {
        //     $filter->add_elements($mform2);
        // }
        // $this->_customdata->mform_hook($mform2);


        
        $atributes=array('size'=>'40');
        
        $mform2->addElement('static','cadena3','',get_string('campo3','report_vivo'));
        $mform2->addElement('text','campo3');
        $mform2->setType('campo3', PARAM_INTEGER);

        //$mform2->addElement('submit','save','salvar');


        $this->add_submit_buttons($mform2);
    }

    /**
     * @param MoodleQuickForm $mform2
     */
    function add_submit_buttons($mform2) {
        $buttons = array();
        $buttons[] = &$mform2->createElement('submit', 'submitbutton', get_string('enviar','report_vivo'));
        //get_string('filter', 'report_vivo'));
        //  $buttons[] = &$mform2->createElement('submit', 'resetbutton', get_string('reset', 'report_vivo'));
        $mform2->addGroup($buttons, 'buttons', '', array(' '), false);

        $mform2->registerNoSubmitButton('reset');
    }
}
