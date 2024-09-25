<?php
/**
 * Wrapper around Google API calls
 *
 * @see https://developers.google.com/google-apps/calendar/
 * @see https://github.com/google/google-api-php-client
 * @see https://developers.google.com/api-client-library/php/auth/service-accounts
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Recurr\Rule;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;

class GoogleGateway
{
    const DATE_FORMAT     = 'Y-m-d';
    const DATETIME_FORMAT = \DateTime::RFC3339;
    const FIELDS = 'description,end,endTimeUnspecified,iCalUID,id,kind,location,locked,'
                 . 'originalStartTime,privateCopy,recurrence,recurringEventId,sequence,'
                 . 'creator,organizer,attendees,source,start,status,summary';

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    public static $logger;
    public static function setLogger(LoggerInterface $logger) { self::$logger = $logger; }
    public static function getLogger() {
        global $LOGGER;

        if (!self::$logger && isset($LOGGER)) {
            self::setLogger($LOGGER);
        }
        return self::$logger;
    }

    private static $client;
    private static $service;

    public static function getClient()
    {
        if (!self::$client) {
            $json = json_decode(file_get_contents(GOOGLE_CREDENTIALS_FILE));
            $credentials = new \Google_Auth_AssertionCredentials(
                $json->client_email,
                ['https://www.googleapis.com/auth/calendar'],
                $json->private_key
            );
            $credentials->sub = GOOGLE_USER_EMAIL;

            self::$client = new \Google_Client();
            self::$client->setClassConfig('Google_Cache_File', 'directory', SITE_HOME.'/Google_Client');
            self::$client->setAssertionCredentials($credentials);
            if (self::$client->getAuth()->isAccessTokenExpired()) {
                self::$client->getAuth()->refreshTokenWithAssertion();
            }
        }
        return self::$client;
    }

    public static function getService()
    {
        if (!self::$service) {
             self::$service = new \Google_Service_Calendar(self::getClient());
        }
        return self::$service;
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list
     * @return CalendarList
     */
    public static function getCalendars()
    {
        $service = self::getService();
        return $service->calendarList->listCalendarList();
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/calendarList/get
     * @param string $calendarId
     * @return CalendarListEntry
     */
    public static function getCalendar($calendarId)
    {
        $service = self::getService();
        return $service->calendarList->get($calendarId);
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/events/list
     * @param string $calendarId
     * @param DateTime $start
     * @param DateTime $end
     * @return array An array of Application\Model\Events
     */
    public static function getEvents($calendarId, \DateTime $start=null, \DateTime $end=null, array $filters=null, $singleEvents=null)
    {
        $events = [];

        $service = self::getService();

        $opts = [
            'fields'       => 'items('.self::FIELDS.')',
            'singleEvents' => $singleEvents
        ];

        if ($start) { $opts['timeMin'] = $start->format(self::DATETIME_FORMAT); }
        if ($end  ) { $opts['timeMax'] = $end  ->format(self::DATETIME_FORMAT); }

        $events = $service->events->listEvents($calendarId, $opts);
        return $events;
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     * @return Event
     */
    public static function getEvent($calendarId, $eventId)
    {
        $service = self::getService();
        return $service->events->get($calendarId, $eventId);
    }

    /**
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return Google_Service_Calendar_Event
     */
    public static function insertEvent($calendarId, \Google_Service_Calendar_Event $event)
    {
        $service = self::getService();
        return $service->events->insert($calendarId, $event);
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     * @param Google_Service_Calendar_Event $patch
     * @return Google_Service_Calendar_Event
     */
    public static function patchEvent($calendarId, $eventId, \Google_Service_Calendar_Event $patch)
    {
        $logger   = self::getLogger();
        $service  = self::getService();
        $response = null;

        if ($logger) {
            $extra = ['calendarId'=>$calendarId, 'eventId'=>$eventId];
            try {
                $response = $service->events->patch($calendarId, $eventId, $patch);
                if (!$response instanceof \Google_Service_Calendar_Event) {
                    $logger->log(Logger::WARN, 'patch/unknownResponse', $extra);
                }
            }
            catch (\Exception $e) {
                $logger->log(Logger::ERR, $e->getMessage(), $extra);
            }
        }
        else {
            try {
                $response = $service->events->patch($calendarId, $eventId, $patch);
            }
            catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $response;
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     */
    public static function deleteEvent($calendarId, $eventId)
    {
        $service = self::getService();
        $service->events->delete($calendarId, $eventId);
    }

	/**
	 * @param string $calendarId
	 * @param Google_Service_Calendar_EventAttendee $attendee
	 */
	public static function attendAllEvents($calendarId, \Google_Service_Calendar_EventAttendee $attendee)
	{
        $logger  = self::getLogger();
        $service = self::getService();

        $opts    = [ 'fields'       => 'items(attendees,id,organizer,status),nextPageToken',
                     'showDeleted'  => false,
                     'singleEvents' => false
                   ];
        $pageToken = 1;

        while  ($pageToken) {
            if ($pageToken !== 1) { $opts['pageToken'] = $pageToken; }

            $events = $service->events->listEvents($calendarId, $opts);

            foreach ($events as $event) {
                # Create a patch
                $attendees = $event->getAttendees();

                if (self::hasAttendee($attendees, $attendee->getEmail()) === false) {
                    $organizer = $event->getOrganizer();
                    if ($organizer) {
                        $attendees[] = $attendee;
                        $patch = new \Google_Service_Calendar_Event();
                        $patch->setAttendees($attendees);

                        self::patchEvent($organizer->getEmail(), $event->id, $patch);
                    }
                    else {
                        if ($event->getStatus() == self::STATUS_CONFIRMED) {
                            $extra = [
                                'calendarId' => $calendarId,
                                'eventId'    => $event->id
                            ];
                            $logger->log(Logger::ERR, "Event with no organizer", $extra);
                        }
                    }
                }
            }
            $pageToken = $events->getNextPageToken();
        }
	}

	/**
	 * @param string $calendarId Calendar that has the events
	 * @param string $email Email of calendar to unattend
	 */
	public static function unattendAllEvents($calendarId, $email)
	{
        $service = self::getService();
        $opts    = [ 'fields'       => 'items(attendees,id,organizer),nextPageToken',
                     'showDeleted'  => false,
                     'singleEvents' => false
                   ];
        $pageToken = 1;

        while ($pageToken) {
            if ($pageToken !== 1) { $opts['pageToken'] = $pageToken; }

            $events = $service->events->listEvents($calendarId, $opts);

            foreach ($events as $event) {
                $attendees = $event->getAttendees();
                $i = self::hasAttendee($attendees, $email);
                if ($i !== false) {
                    $organizer = $event->getOrganizer()->getEmail();

                    unset($attendees[$i]);
                    $patch = new \Google_Service_Calendar_Event();
                    $patch->setAttendees($attendees);

                    self::patchEvent($organizer, $event->id, $patch);
                }
            }
            $pageToken = $events->getNextPageToken();
        }
	}

    /**
     * @param array $attendees
     * @param string $email
     * @return int Index for the email in the attendees array
     */
    private static function hasAttendee($attendees, $email)
    {
        foreach ($attendees as $i => $a) {
            if ($a->getEmail() === $email) { return $i; }
        }
        return false;
    }
}
