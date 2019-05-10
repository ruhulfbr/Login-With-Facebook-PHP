<?php

session_start();
require_once 'Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '623857561415298', // Replace {app-id} with your app id
  'app_secret' => 'bc62e4703f30790e71609f2d5291e5e4',
  'default_graph_version' => 'v3.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost/fblogin/fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';