<?php
/**
 * @copyright 2015-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\AggregatedCalendarsTable;
use Application\Models\GoogleGateway;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

include __DIR__.'/../bootstrap.php';

$logger = GoogleGateway::getLogger();
$logger->log(Logger::INFO, 'Synchronize');

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) {
    $logger->log(Logger::INFO, "Attendee: {$calendar->getAggregation()->getName()} | Calendar: {$calendar->getName()}");
    $calendar->attendAllEvents();
}
