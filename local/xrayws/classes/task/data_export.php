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
 * Data export task class
 *
 * @package    local_xrayws
 * @category   task
 * @copyright  2015 Moodlerooms {@link http://www.moodlerooms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

namespace local_xrayws\task;

class data_export extends \core\task\scheduled_task {

    /**
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('taskname', 'local_xrayws');
    }

    /**
     * @param $value string
     * @return string
     */
    protected function safexml($value) {
        $result = htmlspecialchars(html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8'),
                                    ENT_NOQUOTES,
                                    'UTF-8',
                                    false);
        return $result;
    }

    /**
     * Needs to be implemented
     */
    public function execute() {
        // TODO: do it
        global $DB;

        // Create output file in the temporary directory.
        $tempdir = make_temp_directory('storeexport');
        if (empty($tempdir)) {
            throw new \RuntimeException("error");
        }

        $tempfile = tempnam($tempdir, uniqid('dataexport', true));
        if ($tempfile === false) {
            throw new \RuntimeException("some error");
        }

        $xml = new \XMLWriter();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        if (!$xml->openUri($tempfile)) {
            throw new \RuntimeException("some error");
        }

        $recs = $DB->get_recordset('context');
        $metadata = array_keys($DB->get_columns('context'));
        // Write the data.
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('root');
        $counter = 0;
        foreach ($recs as $rec) {
            $xml->startElement('record');
            foreach ($metadata as $column) {
                $xml->writeElement($column, $this->safexml($rec->$column));
            }
            $xml->endElement(); // End record.
            $counter++;
            if ($counter == 1000) {
                $xml->flush();
                $counter = 0;
            }
        }
        $xml->fullEndElement(); // End root.
        $xml->flush();
        unset($xml);

        // Move the file to propper location.

    }
}
