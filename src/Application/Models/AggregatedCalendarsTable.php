<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class AggregatedCalendarsTable extends TableGateway
{
	public function __construct()
	{
        parent::__construct('aggregatedCalendars', __namespace__.'\AggregatedCalendar');
    }
}