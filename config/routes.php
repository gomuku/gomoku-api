<?php
/**
 * Home page
 */
$app->get('/', '\Api\Controller\UserController:index');

/**
 * Api get token for login action
 */
$app->post('/token', '\Api\Controller\UserController:genToken');
