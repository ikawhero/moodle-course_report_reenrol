<?php

/**
 * Form code snippet for selecting old users
 *
 * @author Shane Elliott, Pukunui Technology
 * @copyright Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course-report-reenrol
 */

require_once($CFG->dirroot.'/lib/formslib.php');


/**
 * Implements the form for the user selection
 */
class reenrol_user_form extends moodleform {
   
    /**
     * Form definition for selecting users
     */
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $logusers = $this->_customdata['logusers'];
        $roles = $this->_customdata['roles'];

        /// Get some strings
        $strrequired = get_string('required');

        /// Hidden fields
        $mform->addElement('hidden', 'id');

        /// Activity details
        $mform->addElement('header', 'selectusers', get_string('selectusers', 'report_reenrol'));
        $mform->addElement('static', 'selectuserstext', '', get_string('selectuserstext', 'report_reenrol'));

        if (!empty($logusers)) {
            foreach ($logusers as $id=>$u) {
                $mform->addElement('advcheckbox', "u$id", fullname($u), null, array('group' => 1));
                $mform->setDefault("u$id", 1);
            }
        }

        $this->add_checkbox_controller(1, '', null);

        $mform->addElement('select', 'roleid', get_string('assignrole', 'report_reenrol'),$roles);

        $this->add_action_buttons(false, get_string('reenrol', 'report_reenrol'));
    }

    /**
     * Perform some server side validation
     *
     * @param array $data submitted form data
     * @return array
     */
    public function validation($data) {

        $errors = array();

        return $errors;
    }

}

?>
