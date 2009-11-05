<?php

/**
 * Display link in course report list
 *
 * @author Shane Elliott, Pukunui Technology
 * @copyright Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course-report-reenrol
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}


if (has_capability('coursereport/reenrol:view', $context) and has_capability('moodle/role:assign', $context)) {
    echo "<p><a href=\"$CFG->wwwroot/course/report/reenrol/index.php?id=$course->id\">".
          get_string('reenrolreport', 'report_reenrol').
         '</a></p>';
}

?>
