<?php
/**
 * Home page
 */
$app->get('/', '\Api\Controller\UserController:index');

/**
 * Api login action
 */
$app->post('/login', '\Api\Controller\UserController:login');

/**
 * Api gen token
 */
$app->post('/token', '\Api\Controller\UserController:token');

/**
 * Api for room management
 */
$app->get('/rooms[/{id}]', '\Api\Controller\RoomController:read');
$app->post('/rooms/join/{id}', '\Api\Controller\RoomController:join');
$app->post('/rooms/leave/{id}', '\Api\Controller\RoomController:leave');
