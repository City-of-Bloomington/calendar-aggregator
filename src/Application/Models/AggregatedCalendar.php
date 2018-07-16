<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class AggregatedCalendar extends ActiveRecord
{
    use GoogleCalendarFields;

    protected $tablename = 'aggregatedCalendars';
    protected $aggregation;

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|string|array $id (ID, email, username)
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
                $sql = 'select * from aggregatedCalendars where id=?';
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('aggregatedCalendars/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
        if (!$this->getAggregation_id() || !$this->getName() || !$this->getGoogle_calendar_id()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save()
	{
        $this->validate();

        $this->attendAllEvents();

        parent::save();
    }

	public function delete()
	{
        $aggregation = $this->getAggregation();
        GoogleGateway::unattendAllEvents($this->getGoogle_calendar_id(), $aggregation->getGoogle_calendar_id());

        parent::delete();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()             { return parent::get('id'); }
	public function getAggregation_id() { return parent::get('aggregation_id'); }
	public function getAggregation()    { return parent::getForeignKeyObject(__namespace__.'\Aggregation', 'aggregation_id'); }

	public function setAggregation_id($i) { parent::setForeignKeyField (__namespace__.'\Aggregation', 'aggregation_id', $i); }
	public function setAggregation   ($o) { parent::setForeignKeyObject(__namespace__.'\Aggregation', 'aggregation_id', $o); }

	public function handleUpdate($post)
	{
        $this->setAggregation_id($post['aggregation_id']);
        $this->setGoogle_calendar_id($post['google_calendar_id']);
	}

	//----------------------------------------------------------------
	// Custom function
	//----------------------------------------------------------------
    public static function generate_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public function attendAllEvents()
    {
        $aggregation = $this->getAggregation();

        $attendee = new \Google_Service_Calendar_EventAttendee([
            'email'          => $aggregation->getGoogle_calendar_id(),
            'displayName'    => $aggregation->getName(),
            'responseStatus' => 'accepted'
        ]);
        GoogleGateway::attendAllEvents($this->getGoogle_calendar_id(), $attendee);
    }
}
