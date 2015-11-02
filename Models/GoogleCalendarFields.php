<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

trait GoogleCalendarFields
{
	public function getName()               { return parent::get('name'); }
	public function getGoogle_calendar_id() { return parent::get('google_calendar_id'); }

	public function setName              ($s) { parent::set('name', $s); }
	public function setGoogle_calendar_id($s)
	{
        $gateway = new GoogleGateway();
        $calendar = $gateway->getCalendar($s);
        $this->setName($calendar->summary);
        parent::set('google_calendar_id', $s);
    }
}