<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\AggregatedCalendarsTable;
use Application\Models\GoogleGateway;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

include __DIR__.'/../bootstrap.php';

$writer = new Stream(SITE_HOME.'/sync_log.txt');
$logger = new Logger();
$logger->addWriter($writer);
$logger->log(Logger::INFO, 'Synchronize');

GoogleGateway::setLogger($logger);

$table = new AggregatedCalendarsTable();
$list = $table->find();
foreach ($list as $calendar) { $calendar->attendAllEvents(); }
