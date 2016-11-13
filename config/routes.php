<?php
/**
 * Home page
 */
$app->get('/', '\Api\Controller\UserController:index');

/**
 * Api get token for login action
 */
$app->post('/get_token', '\Api\Controller\UserController:getToken');
