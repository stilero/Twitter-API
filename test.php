<?php
function buildBaseString($baseURI, $method, $params)
{
    $r = array(); 
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value); 
    }
    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r)); 
}

function buildAuthorizationHeader($oauth)
{
    $r = 'Authorization: OAuth '; 
    $values = array(); 
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\""; 
    $r .= implode(', ', $values); 
    return $r; 
}
$url = "https://api.twitter.com/1.1/statuses/update.json";
$url = "http://www.stilero.com/pingtest.php";

$oauth_access_token = "19602888-A3bNfPSzZzca6ydGmL3oN5fMyAnk0RpLtxA7vbNri";
$oauth_access_token_secret = "Abkw6VwuS9rdQ54JwSchkfnCibP4gWmKkunda5dBWao";
$consumer_key = "A7NX7yIAGXuk3CDvTEDzLg";
$consumer_secret = "Nnz3HrNjNCE6inhGVTjA6FWl9lleYnL2gUu6lf2PZd0";

$oauth = array( 'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $oauth_access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0');
$base_info = buildBaseString($url, 'GET', $oauth);
$composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
$oauth['oauth_signature'] = $oauth_signature;
//$params = array('status' => 'HEllo, just testing');
$header = array(buildAuthorizationHeader($oauth), 'Expect:');
$options = array( CURLOPT_HTTPHEADER => $header,
                  CURLOPT_HEADER => false,
                  CURLOPT_URL => $url,
                  //CURLOPT_POST => true,
                  //CURLOPT_POSTFIELDS => http_build_query($params),
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_SSL_VERIFYPEER => false);

$feed = curl_init();
curl_setopt_array($feed, $options);
$json = curl_exec($feed);
curl_close($feed);

$twitter_data = json_decode($json);

?>
<pre><?php print_r($twitter_data); ?></pre>
