<?php

/**
 * Display a list of past users and allow them to be re-enrolled
 * in the course
 *
 * @author Shane Elliott, Pukunui Technology
 * @copyright Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course-report-reenrol
 */


require_once('../../../config.php');
require_once($CFG->dirroot.'/course/report/reenrol/index_form.php');


$id = required_param('id',PARAM_INT);       // course id

if (!$course = get_record('course', 'id', $id)) {
    error('Course id is incorrect.');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('coursereport/reenrol:view', $context);
require_capability('moodle/role:assign', $context);

add_to_log($course->id, 'course', 'report reenrol', "report/renrol/index.php?id=$course->id", $course->id);

/// Let's get a list of current users
if (!($users = get_users_by_capability($context, 'moodle/course:view', 'u.id'))) {
    $notinsql = '';
} else {
    $notinsql = ' AND u.id NOT IN ('.implode(',', array_keys($users)).')';
}

/// Now let's get a list of users from the logs
$sql = "SELECT u.id, u.firstname, u.lastname, MAX(l.time) AS lastaccess 
        FROM {$CFG->prefix}user u 
        INNER JOIN {$CFG->prefix}log l ON l.userid=u.id 
        WHERE l.course=$id
        $notinsql 
        GROUP BY u.id, u.firstname, u.lastname";

if (!($logusers = get_records_sql($sql))) {
    $logusers = array();
}

/// Get the assignable roles
$roles  = get_assignable_roles($context, 'name', ROLENAME_BOTH);


/// Set up the user form
$reenroluserform = new reenrol_user_form(null, compact('logusers', 'roles'));

/// Set the default data
$defaultdata = new stdclass;
$defaultdata->id = $id;
$defaultdata->roleid = empty($course->defaultrole) ? $CFG->defaultcourseroleid : $course->defaultrole;
$reenroluserform->set_data($defaultdata);


/// Has form been submitted?
if ($data = $reenroluserform->get_data()) {

    $reenrolusers = array();
    /// Let's cycle through the logusers and find which have been selected
    foreach ($logusers as $uid=>$lu) {
        $field = "u$uid";
        if (!empty($data->$field)) {
            $reenrolusers[$uid] = $lu;
        }
    }

    $role = get_record('role', 'id', $data->roleid);

    /// Let's assign the role to the users
    if (!empty($reenrolusers)) {
        $now = time();
        $result = new stdclass;
        $result->role = $role->name;
        $names = array(); /// So we can print a message

        /// This loop does the work of reenrolling users
        foreach ($reenrolusers as $uid=>$ru) {
            role_assign($role->id, $uid, 0, $context->id, 0, 0, 0, 'manual', $now);
            $names[] = fullname($ru);
        }

        $result->names = implode(', ', $names);
    }
}

$strreenrolreport  = get_string('reenrolreport', 'report_reenrol');
$strreports        = get_string('reports');

$navlinks = array();
$navlinks[] = array('name' => $strreports, 
                    'link' => "$CFG->wwwroot/course/report.php?id=$course->id", 
                    'type' => 'misc');
$navlinks[] = array('name' => $strreenrolreport, 
                    'link' => null, 
                    'type' => 'misc');

print_header("$course->shortname: $strreenrolreport", $course->fullname, build_navigation($navlinks));

print_heading(format_string($course->fullname));

/// Have we just reenrolled some users?
if (!empty($reenrolusers)) {
    notify(get_string('usersreenrolled', 'report_reenrol', $result), 'notifysuccess');
    print_continue($CFG->wwwroot.'/course/view.php?id='.$id);

/// Can we find any users to reenrol?
} elseif (empty($logusers)) {
    notify(get_string('nooldusers', 'report_reenrol'));
    print_continue($CFG->wwwroot.'/course/view.php?id='.$id);

/// Print out the form to select users
} else {
    $reenroluserform->display();
}

print_footer($course);


?>
