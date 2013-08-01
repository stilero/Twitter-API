<?php
foreach (glob("../*.php") as $filename)
{
    require_once $filename;
}
$Consumer = new OauthClient("A7NX7yIAGXuk3CDvTEDzLg", "Nnz3HrNjNCE6inhGVTjA6FWl9lleYnL2gUu6lf2PZd0");
$User = new OauthUser("19602888-A3bNfPSzZzca6ydGmL3oN5fMyAnk0RpLtxA7vbNri", "Abkw6VwuS9rdQ54JwSchkfnCibP4gWmKkunda5dBWao");
//$Server = new OauthServer($Consumer, $User);
$Tweets = new TwitterStatus($Consumer, $User);
$resp = $Tweets->update("Testing my new app");
print_r($resp);
?>
