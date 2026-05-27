<?php
/**
 * @copyright 2015-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\AggregatedCalendarsTable;
use Application\Models\GoogleGateway;

include __DIR__.'/../bootstrap.php';

$log = GoogleGateway::getLog();

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) {
    fwrite($log, date(GoogleGateway::DATETIME_FORMAT)." INFO Attendee: {$calendar->getAggregation()->getName()} | Calendar: {$calendar->getName()}\n");
    $calendar->attendAllEvents();
}
