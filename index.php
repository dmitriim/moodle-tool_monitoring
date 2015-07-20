<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  Moodle state for monitoring.
 *
 * @package    tool_monitoring
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2015 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_UPGRADE_CHECK', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/admin/tool/monitoring/classes/tool_monitoring.php');

// Defaults.
$remotepassword = MONITORINGPASS;
$json = true;

$config = tool_monitoring_get_config();

if (isset($config->remotepassword)) {
    $remotepassword = $config->remotepassword;
}
// This script is being called via the web, so check the password if there is one.
if ($remotepassword != '') {
    $pass = optional_param('password', '', PARAM_RAW);
    if ($pass != $remotepassword) {
        // Wrong password.
        echo html_writer::tag('div', get_string('error:password', 'tool_monitoring'));
        exit;
    }
}
// Check if we don't want json.
if (isset($config->jsonenabled) and empty($config->jsonenabled)) {
    $json = false;
}

$monitoring = new tool_monitoring();
$results = $monitoring->do_checks();

if ($json) {
    echo json_encode($results);
} else {
    foreach ($results as $name => $result) {
        echo html_writer::tag('div', get_string($name, 'tool_monitoring', $result['result']));
        if (isset($result['info'])) {
            echo html_writer::tag('div', get_string('info', 'tool_monitoring') . $result['info']);
        }
    }
}


