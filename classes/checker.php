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

require_once($CFG->dirroot.'/admin/tool/monitoring/lib.php');

defined('MOODLE_INTERNAL') || die;

class tool_monitoring_checker {

    private $checks;
    private $params;
    private $config;
    private $displayinfo;

    /**
     * Constructor.
     *
     * @param array $checks A list of monitoring checks. Each element has to have a function with the same name.
     * @param array $params A list of parameters to pass to an elemet function.
     */
    public function __construct($checks = null, $params = null) {

        $delay = 24;
        $folders = array('filedir', 'temp');

        // Get config.
        $this->config = get_config('tool_monitoring');

        if (isset($this->config->displayinfo)) {
            $this->displayinfo = $this->config->displayinfo;
        } else {
            $this->displayinfo = true;
        }

        // Get tasks to check.
        if (empty($checks)) {
            $this->checks = array('cron', 'db', 'dataroot');
        } else {
            $this->checks = $checks;
        }

        // Get parameterss.
        if (!empty($params)) {
            $this->params = $params;
        } else {
            $this->params = array(
                    'cron'     => array($delay),
                    'db'       => array(),
                    'dataroot' => array('folders' => $folders)
            );
        }

        if (!isset($this->params['cron']['delay'])) {
            $this->params['cron']['delay'] = $delay;
        }

        if (!isset($this->params['dataroot']['folders'])) {
            $this->params['dataroot']['folders'] = array('folders' => $folders);
        }
    }
    /**
     * Run checks and retun a result.
     *
     * @return array
     */
    public function do_checks() {
        $results = array();

        // Get results.
        foreach ($this->checks as $check) {
            $results[$check] = $this->{$check}();
        }

        $overall = get_string('alive', 'tool_monitoring');

        foreach ($results as $check => $result) {
            if ($result['result']) {
                $results[$check]['result'] = get_string('ok', 'tool_monitoring');
                unset($results[$check]['info']);
            } else {
                $results[$check]['result'] = get_string('error', 'tool_monitoring');
                $overall = get_string('down', 'tool_monitoring');
                if (!$this->displayinfo) {
                    unset($results[$check]['info']);
                }
            }
        }
        // Overall.
        $results['overall']['result'] = $overall;

        return $results;
    }
    /**
     * Check if Moodle is older then Moodle 2.7
     *
     * @return boolean
     */
    private function is_legacy_cron() {
        global $CFG;

        if ((float)$CFG->version < 2014051200) {
            return true;
        }

        return false;
    }

    /**
     * Check cron
     *
     * @return array
     */
    private function cron() {
        global $DB;

        $errors = null;
        $delay = $this->params['cron']['delay'];

        if ($this->is_legacy_cron()) {
            $sql = 'SELECT MAX(lastcron) FROM {modules}';
        } else {
            $sql = 'SELECT MAX(lastruntime) FROM {task_scheduled}';
        }

        $lastcron = $DB->get_field_sql($sql);
        $result = ($lastcron > time() - 3600 * $delay);

        if (!$result) {
            $errors = get_string('cronwarning', 'tool_monitoring', $delay);
        }

        return array('result' => $result, 'info' => $errors);
    }
    /**
     * Check db
     *
     * @return array
     */
    private function db() {
        global $DB;

        $errors = null;

        $table = 'config';
        $dbmonitoring = new stdClass();
        $dbmonitoring->name = 'dbmonitoring';
        $dbmonitoring->value = time();

        try {
            // Check if record exists.
            $select = $DB->get_record($table, array('name' => $dbmonitoring->name));
            // Delete it.
            if ($select) {
                $delete = $DB->delete_records($table, array('id' => $select->id));
            }
            // Try to insert.
            $insert = $DB->insert_record($table, $dbmonitoring);
            if ($insert) {
                // Try to select.
                $select = $DB->get_record($table, array('name' => $dbmonitoring->name));
                $select->value = time();
                // Try to update.
                $update = $DB->update_record($table, $select);
                // Try to delete.
                $delete = $DB->delete_records($table, array('id' => $select->id));
            }

            // Try to create and delete temp table.
            $temptables = true;
            $dbman = $DB->get_manager();
            $temptable = new xmldb_table('monitoring_temp_table');
            $temptable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $temptable->add_field('name', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
            $temptable->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_temp_table($temptable);
            // Drop temp table.
            $dbman->drop_table($temptable);

            $result = $insert * $update * $delete * $temptables;
        } catch (Exception $e) {
            $errors .= $e->getMessage();
            $errors .= $e->debuginfo;
            $result = false;
        }

        return  array('result' => $result, 'info' => $errors);
    }
    /**
     *  Check dataroot
     *
     * @return array
     */
    private function dataroot() {
        global $CFG;

        $errors = null;
        $result = true;
        $folders = $this->params['dataroot']['folders'];
        $newdirname = 'datarootmonitoring';
        $testfile = $CFG->dataroot . "/datarootmonitoring.test";

        // Check all directories.
        foreach ($folders as $folder) {
            $dir = $CFG->dataroot . '/' . $folder;
            if (!is_readable($dir)) {
                $result = false;
                $errors .= "{$dir} is not readable.";
                break;
            }
            if (!is_writable($dir)) {
                $result = false;
                $errors .= "{$dir} is not writable.";
                break;
            } else {
                $newdir = $dir . '/' . $newdirname;
                if (!is_dir($newdir)) {
                    if (!mkdir($newdir)) {
                        $result = false;
                        $errors .= "Can't create {$newdir}";
                        break;
                    } else if (!rmdir($newdir)) {
                        $result = false;
                        $errors .= "Can't delete {$newdir}";
                        break;
                    }
                } else {
                    if (!rmdir($newdir)) {
                        $result = false;
                        $errors .= "Can't delete {$newdir}";
                        break;
                    }
                    if (!mkdir($newdir)) {
                        $result = false;
                        $errors .= "Can't create {$newdir}";
                        break;
                    } else if (!rmdir($newdir)) {
                        $result = false;
                        $errors .= "Can't delete {$newdir}";
                        break;
                    }
                }
            }
        }

        // Try to write a file.
        $size = file_put_contents($testfile, 'Hello World. Testing!');
        if ($size !== 21) {
            $result = false;
            $errors .= "Can't write file $testfile. Sitedata is not writable.";
        }

        return  array('result' => $result, 'info' => $errors);
    }
}