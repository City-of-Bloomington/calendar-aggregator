<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Aggregation extends ActiveRecord
{
    protected $tablename = 'aggregations';

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
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from aggregations where id=?';
				}
				else {
					$sql = 'select * from aggregations where google_calendar_id=?';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('aggregations/unknown');
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
        if (!$this->getName() || !$this->getGoogle_calendar_id()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save  () { parent::save(); }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                 { return parent::get('id'); }
	public function getName()               { return parent::get('name'); }
	public function getGoogle_calendar_id() { return parent::get('google_calendar_id'); }

	public function setName              ($s) { parent::set('name',               $s); }
	public function setGoogle_calendar_id($s)
	{
        $gateway = new GoogleGateway();
        $calendar = $gateway->getCalendar($s);
        $this->setName($calendar->summary);
        parent::set('google_calendar_id', $s);
    }

	public function handleUpdate($post)
	{
        $this->setGoogle_calendar_id($post['google_calendar_id']);
	}
}