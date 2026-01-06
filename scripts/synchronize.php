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

$log    =      fopen(SITE_HOME.'/sync.log', 'a');
$writer = new Stream(SITE_HOME.'/sync.log');
$logger = new Logger();
$logger->addWriter($writer);
$logger->log(Logger::INFO, 'Synchronize');

GoogleGateway::setLogger($logger);

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) {
    fwrite($log, "Attendee: {$calendar->getAggregation()->getName()} | Calendar: {$calendar->getName()}\n");
    $calendar->attendAllEvents();
}
