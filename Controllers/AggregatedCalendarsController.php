<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Aggregation;
use Application\Models\AggregatedCalendar;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\View;

class AggregatedCalendarsController extends Controller
{
    public function index()
    {
    }

    public function update()
    {
        if (isset($_REQUEST['id'])) {
            try { $calendar = new AggregatedCalendar($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (!isset($calendar) && !empty($_REQUEST['aggregation_id'])) {
            try {
                $aggregation = new Aggregation($_REQUEST['aggregation_id']);
                $calendar = new AggregatedCalendar();
                $calendar->setAggregation($aggregation);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (!isset($calendar)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if (isset($_POST['id'])) {
            try {
                $calendar->handleUpdate($_POST);
                $calendar->save();
                header('Location: '.View::generateUrl('aggregations.view', ['id'=>$calendar->getAggregation_id()]));
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $this->template->blocks[] = new Block('aggregatedCalendars/updateForm.inc', ['calendar'=>$calendar]);
        $this->template->blocks[] = new Block('aggregations/view.inc', ['aggregation'=>$calendar->getAggregation()]);
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            try {
                $calendar = new AggregatedCalendar($_GET['id']);
                $aggregation_id = $calendar->getAggregation_id();
                $calendar->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.View::generateUrl('aggregations.view', ['id'=>$aggregation_id]));
        exit();
    }
}