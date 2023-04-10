<?php



require_once("$CFG->libdir/formslib.php");



class coursesettingsform extends moodleform
{
    public function definition()
    {
        // TODO: Implement definition() method.
        $OPTIONS =[
            '0' => 'No',
            '1' => 'Yes'

        ];
        $courseid = $this->_customdata['id'];



        $mform = $this->_form;
        $mform->addElement('select', 'enable',"Enable",$OPTIONS);
        $mform->setType('enable', PARAM_INT);
        $mform->addElement('hidden','id',$courseid);
        $mform->setType('id',PARAM_INT);
        $this->add_action_buttons();


    }


}

