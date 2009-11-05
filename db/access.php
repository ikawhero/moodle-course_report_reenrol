<?php

/**
 * Role permissions
 *
 * @author Shane Elliott, Pukunui Technology
 * @copyright Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course-report-reenrol
 */

$coursereport_reenrol_capabilities = array(

    'coursereport/reenrol:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:viewreports',
    )
);

?>
