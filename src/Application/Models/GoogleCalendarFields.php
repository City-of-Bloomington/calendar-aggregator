<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

trait GoogleCalendarFields
{
    protected $google_calendar; // Google_Service_Calendar_CalendarListEntry

	public function getName()               { return parent::get('name'); }
	public function getGoogle_calendar_id() { return parent::get('google_calendar_id'); }

	public function setName($s) { parent::set('name', $s); }

	/**
	 * @see https://developers.google.com/google-apps/calendar/v3/reference/calendarList
	 * @return Google_Service_Calendar_CalendarListEntry
	 */
	public function getGoogle_calendar()
	{
        if (!$this->google_calendar && $this->getGoogle_calendar_id()) {
             $this->google_calendar = GoogleGateway::getCalendar($this->getGoogle_calendar_id());
        }
        return $this->google_calendar;
	}

	/**
	 * @param string $s
	 */
	public function setGoogle_calendar_id($s)
	{
        $this->google_calendar = GoogleGateway::getCalendar($s);
        if ($this->google_calendar) {
            $this->setName($this->google_calendar->summary);
            parent::set('google_calendar_id', $s);
        }
    }
}