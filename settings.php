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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/admin/tool/monitoring/lib.php');

if ($hassiteconfig) {

    $settings = new admin_settingpage('tool_monitoring', get_string('pluginname', 'tool_monitoring'));

    $settings->add(new admin_setting_configpasswordunmask('tool_monitoring/remotepassword',
            get_string('settings:remotepassword', 'tool_monitoring'),
            get_string('settings:remotepassword_desc', 'tool_monitoring'), MONITORINGPASS));

    $settings->add(new admin_setting_configcheckbox('tool_monitoring/jsonenabled',
            get_string('settings:jsonenabled', 'tool_monitoring'),
            get_string('settings:jsonenabled_desc', 'tool_monitoring'), 1));

    $settings->add(new admin_setting_configcheckbox('tool_monitoring/displayinfo',
            get_string('settings:displayinfo', 'tool_monitoring'),
            get_string('settings:displayinfo_desc', 'tool_monitoring'), 1));

    $ADMIN->add('tools', $settings);
}
