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

$string['pluginname'] = 'Moodle monitoring';

$string['alive'] = 'alive';
$string['cron'] = 'Cron: {$a}';
$string['cronwarning'] = 'The maintenance script has not been run for at least {$a} hours.';
$string['db'] = 'Database: {$a}';
$string['dataroot'] = 'Dataroot: {$a}';
$string['down'] = 'down';
$string['error'] = 'error';
$string['error:password'] = 'Access Password Wrong';
$string['info'] = 'info: ';
$string['ok'] = 'good';
$string['overall'] = 'Overall: {$a}';
$string['settings:remotepassword'] = 'Password for remote access';
$string['settings:remotepassword_desc'] = 'This means that the monitoring script cannot be run from a web browser without supplying the password using the following form of URL:<pre>
    ' . $CFG->wwwroot . '/admin/tool/monitoring/index.php?password=opensesame
</pre>If this is left empty, no password is required.';
$string['settings:displayinfo'] = 'Display debug messages';
$string['settings:displayinfo_desc'] = 'If enabled debug messages will be displayed';
$string['settings:jsonenabled'] = 'Use JSON';
$string['settings:jsonenabled_desc'] = 'If enabled the output will be in JSON format';