<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\AggregatedCalendarsTable;

include '../configuration.inc';

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) { $calendar->attendAllEvents(); }
