<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Block;
use Blossom\Classes\Template;

include '../bootstrap.php';

// Create the default Template
$template = !empty($_REQUEST['format'])
	? new Template('default',$_REQUEST['format'])
	: new Template('default');

$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $ROUTES->match($p, $_SERVER);
if ($route) {
    if (isset($route->params['controller']) && isset($route->params['action'])) {

        $role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';

        if (   $ACL->hasResource($route->params['controller'])
            && $ACL->isAllowed($role, $route->params['controller'], $route->params['action'])) {

            $controller = 'Application\\Controllers\\'.ucfirst($route->params['controller']).'Controller';
            $action     = $route->params['action'];

            if (!empty($route->params['id'])) {
                    $_GET['id'] = $route->params['id'];
                $_REQUEST['id'] = $route->params['id'];
            }

            $c = new $controller($template);
            $c->$action();
        }
        else {
            header('HTTP/1.1 403 Forbidden', true, 403);
            $template->blocks[] = new Block('errorMessages/noAccessAllowed.inc');
        }
    }
}
else {
    $f = $ROUTES->getFailedRoute();
	header('HTTP/1.1 404 Not Found', true, 404);
	$template->blocks[] = new Block('404.inc');
}

echo $template->render();
