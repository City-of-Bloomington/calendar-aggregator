<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;
use Application\Models\Aggregation;
use Application\Models\AggregationsTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\View;

class AggregationsController extends Controller
{
    public function index()
    {
        $table = new AggregationsTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('aggregations/list.inc', ['aggregations'=>$list]);
    }

    public function view()
    {
        if (!empty($_GET['id'])) {
            try { $aggregation = new Aggregation($_GET['id']); }
            catch (\Exception $e) { }
        }
        if (!isset($aggregation)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        $this->template->blocks[] = new Block('aggregatedCalendars/list.inc', ['aggregation'=>$aggregation]);
        $this->template->blocks[] = new Block('aggregations/view.inc',        ['aggregation'=>$aggregation]);
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $aggregation = new Aggregation($_REQUEST['id']); }
            catch (\Exception $e) { }
        }
        else { $aggregation = new Aggregation(); }

        if (!isset($aggregation)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if (isset($_POST['id'])) {
            try {
                $aggregation->handleUpdate($_POST);
                $aggregation->save();

                header('Location: '.View::generateUrl('aggregations.view', ['id'=>$aggregation->getId()]));
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $this->template->blocks[] = new Block('aggregations/updateForm.inc', ['aggregation'=>$aggregation]);
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            try {
                $aggregation = new Aggregation($_GET['id']);
                $aggregation->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.View::generateUrl('aggregations.index'));
        exit();
    }
}