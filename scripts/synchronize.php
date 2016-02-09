<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\AggregatedCalendarsTable;
use Application\Models\GoogleGateway;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

include __DIR__.'/../configuration.inc';

$writer = new Stream(SITE_HOME.'/sync_log.txt');
$logger = new Logger();
$logger->addWriter($writer);
$logger->log(Logger::INFO, 'Synchronize');

GoogleGateway::setLogger($logger);

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) { $calendar->attendAllEvents(); }
