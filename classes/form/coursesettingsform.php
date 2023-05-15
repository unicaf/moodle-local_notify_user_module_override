<?php



require_once("$CFG->libdir/formslib.php");
require_once 'classes/checkStatus.php';


class coursesettingsform extends moodleform
{

    //Form Setup
    public function definition()
    {

        $OPTIONS =[
            '0' => 'No',
            '1' => 'Yes'

        ];
        $courseid = $this->_customdata['id'];


        $value_from_database_enable = new checkStatusClass($courseid);
        $value_from_database_enable = $value_from_database_enable->is_enabled();
        $mform = $this->_form;
        $mform->addElement('select', 'enable',"Enable",$OPTIONS);
        $mform->setType('enable', PARAM_INT);
        //By Default in Database is set to Yes (1) when a user override is created
        $mform->setDefault('enable',$value_from_database_enable->enable);
        $mform->addElement('hidden','id',$courseid);
        $mform->setType('id',PARAM_INT);
        $this->add_action_buttons();


    }


}

