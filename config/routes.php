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
