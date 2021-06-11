<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

$ACL = new Acl();
$ACL->addRole(new Role('Anonymous'))
		 ->addRole(new Role('Public'), 'Anonymous')
		 ->addRole(new Role('Staff'), 'Public')
		 ->addRole(new Role('Administrator'), 'Staff');

/**
 * Declare all the resources
 */
$ACL->addResource(new Resource('index'));
$ACL->addResource(new Resource('people'));
$ACL->addResource(new Resource('users'));
$ACL->addResource(new Resource('login'));

$ACL->addResource(new Resource('aggregations'));
$ACL->addResource(new Resource('aggregatedCalendars'));

/**
 * Assign permissions to the resources
 */
$ACL->allow(null,'login');

// Permissions for unauthenticated browsing
$ACL->allow(null, ['index'], ['index']);
$ACL->allow(null, ['aggregations'], ['index', 'view']);

// Allow Staff to do stuff
$ACL->allow('Staff', ['people'], ['index','view']);
$ACL->allow('Staff', ['aggregations'], ['update']);


// Administrator is allowed access to everything
$ACL->allow('Administrator');
