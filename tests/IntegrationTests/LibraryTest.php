<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once '../../configuration.inc';

class LibraryTest extends PHPUnit_Framework_TestCase
{
    public function testGoogleGateway()
    {
        $gateway = new Application\Models\GoogleGateway();
        $this->assertTrue($gateway instanceof Application\Models\GoogleGateway);

        $service = $gateway::getService();
        $this->assertTrue($service instanceof \Google_Service_Calendar);
    }
}
