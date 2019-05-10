<?php
session_start();
require_once 'Facebook/autoload.php';
require_once('Facebook/FacebookRequest.php');
$fb = new Facebook\Facebook([
  'app_id' => '623857561415298', // Replace {app-id} with your app id
  'app_secret' => 'bc62e4703f30790e71609f2d5291e5e4',
  'default_graph_version' => 'v3.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}


function doCurl($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = json_decode(curl_exec($ch), true);
  curl_close($ch);
  return $data;
}
echo '<h3>User Info</h3>';
echo "<pre>";
$endpoint_url = 'https://graph.facebook.com/v3.3/me?fields=id,name,first_name,last_name,email,cover,link,gender,timezone,updated_time,verified,birthday,location,age_range,picture.type(large)&access_token='.$accessToken->getValue();
$output = doCurl($endpoint_url);
print_r($output);




// id
// cover
// name
// first_name
// last_name
// age_range
// link
// gender
// locale
// picture
// timezone
// updated_time
// verified


//  $fbUser = $fb->request('get', '/me?fields=id,first_name,last_name,email,link,gender,picture');
// echo "<pre>";
//  print_r($fbUser);


// Logged in
echo '<h3>Access Token</h3>';
echo "<pre>";
print_r($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken->getValue());
echo '<h3>Metadata</h3>';
echo "<pre>";
print_r($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('623857561415298'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }

  echo '<h3>Long-lived</h3>';
  var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');
?>

<h1><a href="https://localhost/fblogin/login.php">back</a></h1>