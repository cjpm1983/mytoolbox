<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Filter form
 *
 * @author Carlos Palacios
 * @package report_vivo
 */
class vivo_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'filterheader', get_string('form1header','report_vivo'));

        // foreach ($this->_customdata->get_filters() as $filter) {
        //     $filter->add_elements($mform);
        // }
        // $this->_customdata->mform_hook($mform);


        
        $atributes=array('size'=>'40');
        
        $mform->addElement('static','cadena1','',get_string('campo1','report_vivo'));
        $mform->addElement('text','campo');
        $mform->setType('campo', PARAM_TEXT);
        
        $mform->addElement('static','cadena2','',get_string('campo2','report_vivo'));
        $mform->addElement('text','filtro');
        $mform->setType('filtro', PARAM_TEXT);

        //$mform->addElement('submit','save','salvar');


        $this->add_submit_buttons($mform);
    }

    /**
     * @param MoodleQuickForm $mform
     */
    function add_submit_buttons($mform) {
        $buttons = array();
        $buttons[] = &$mform->createElement('submit', 'submitbutton', get_string('enviar','report_vivo'));
        //get_string('filter', 'report_vivo'));
        //  $buttons[] = &$mform->createElement('submit', 'resetbutton', get_string('reset', 'report_vivo'));
        $mform->addGroup($buttons, 'buttons', '', array(' '), false);

        $mform->registerNoSubmitButton('reset');
    }
}
