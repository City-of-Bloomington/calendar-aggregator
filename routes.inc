<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$rf = new \Aura\Router\RouterFactory(BASE_URI);
$ROUTES = $rf->newInstance();

$ROUTES->add('home', '/')->setValues(['controller'=>'index', 'action'=>'index']);

$ROUTES->attach('login', '/login', function ($r) {
    $r->setValues(['controller'=>'login']);

    $r->add('index',  '')       ->addValues(['action'=>'index' ]);
    $r->add('login',  '/login') ->addValues(['action'=>'login' ]);
    $r->add('logout', '/logout')->addValues(['action'=>'logout']);
});

$ROUTES->attach('aggregations', '/aggregations', function ($r) {
    $r->setTokens(['id'=>'[0-9]+']);
    $r->setValues(['controller'=>'aggregations']);

    $r->add('index',  '')            ->addValues(['action'=>'index' ]);
    $r->add('view',   '/{id}')       ->addValues(['action'=>'view'  ]);
    $r->add('update', '/update{/id}')->addValues(['action'=>'update']);
    $r->add('delete', '/delete/{id}')->addValues(['action'=>'delete']);
    $r->add('sync',   '/sync/{id}')  ->addValues(['action'=>'sync'  ]);
});

$ROUTES->attach('aggregatedCalendars', '/aggregatedCalendars', function ($r) {
    $r->setTokens(['id'=>'[0-9]+']);
    $r->setValues(['controller'=>'aggregatedCalendars']);

    $r->add('update', '/update{/id}')->addValues(['action'=>'update']);
    $r->add('delete', '/delete/{id}')->addValues(['action'=>'delete']);
});

$ROUTES->attach('people', '/people', function ($r) {
    $r->setTokens(['id'=>'[0-9]+']);
    $r->setValues(['controller'=>'people']);

    $r->add('index',  '')            ->addValues(['action'=>'index' ]);
    $r->add('view',   '/{id}')       ->addValues(['action'=>'view'  ]);
    $r->add('update', '/update{/id}')->addValues(['action'=>'update']);
});

$ROUTES->attach('users', '/users', function ($r) {
    $r->setTokens(['id'=>'[a-z0-9]+']);
    $r->setValues(['controller'=>'users']);

    $r->add('index',  '')             ->addValues(['action'=>'index' ]);
    $r->add('update', '/update{/id}') ->addValues(['action'=>'update']);
    $r->add('delete', '/delete/{id}') ->addValues(['action'=>'delete']);
});
