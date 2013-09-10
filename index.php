<?php

require 'facebook.php';

$facebook = new Facebook(array(
  'appId'  => '124879167617493',
  'secret' => '568e9e1be84db7a2b192f8543adca30b',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
  }
}

?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <body>
    <?php if ($user) { ?>
      Your user profile is
      <pre>
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>

      </pre>
      <pre>
        <?php print htmlspecialchars(print_r($facebook->getAccessToken(), true)) ?>
      </pre>
    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
    <script type="text/javascript" src="jquery-1.10.2.min.js"></script>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '124879167617493', // App ID
          channelUrl : 'http(s)://www.facebook.com/CurrentOfAir', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });

        FB.getLoginStatus(function(response) {
          var page_id = "229661030501037";
          if (response && response.authResponse) {
            var user_id = response.authResponse.userID;
            var fql_query = "SELECT uid FROM page_fan WHERE page_id = "+page_id+"and uid="+user_id;
            FB.Data.query(fql_query).wait(function(rows) {
              if (rows.length == 1 && rows[0].uid == user_id) {
                console.log("LIKE");
                $('#container_notlike').hide();
                $('#container_like').show();
              } else {
                console.log("NO LIKEY");
                 $('#container_like').hide();
                $('#container_notlike').show();
              }
            });
          } else {
            FB.login(function(response) {
              if (response && response.authResponse) {
                var user_id = response.authResponse.userID;
                var fql_query = "SELECT uid FROM page_fan WHERE page_id = "+page_id+"and uid="+user_id;
                FB.Data.query(fql_query).wait(function(rows) {
                  if (rows.length == 1 && rows[0].uid == user_id) {
                    console.log("LIKE");
                    $('#container_notlike').hide();
                    $('#container_like').show();
                  } else {
                    console.log("NO LIKEY");
                    $('#container_like').hide();
                    $('#container_notlike').show();
                  }
                });
              } else {
                console.log("NO LIKEY");
                $('#container_like').hide();
                $('#container_notlike').show();
              }
            }, {scope: 'user_likes'});
          }
        });
      };

      // Load the SDK Asynchronously
      (function(d){
        var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
      }(document));
    </script>
     <div id="container_notlike">
      YOU DON'T LIKE ME :(
    </div>

    <div id="container_like">
      YOU LIKE ME :)
    </div>

  </body>
</html>
