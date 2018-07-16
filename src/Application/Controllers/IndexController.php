<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Controller;
use Blossom\Classes\View;

class IndexController extends Controller
{
	public function index()
	{
        header('Location: '.View::generateUrl('aggregations.index'));
        exit();
	}
}
