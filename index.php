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
<head>
  <script type="text/javascript" >

  window.fbAsyncInit = function() {
    function gotoQR()
    {
      var myMessage="<?php echo $facebook->getAccessToken(); ?>";
      window.open("http://jameskong.local/app/canvas/testQRCode.php?data="+myMessage,'_self');
    }
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
    FB.Event.subscribe('edge.create', function(response) {
      gotoQR();
    });

    FB.Event.subscribe('edge.remove', function(response) {

      alert("unlike");
    });
    FB.getLoginStatus(function(response) {
      var page_id = "535448459824840";
      if (response && response.authResponse) {
        var user_id = response.authResponse.userID;
        var fql_query = "SELECT uid FROM page_fan WHERE page_id = "+page_id+"and uid="+user_id;
        FB.Data.query(fql_query).wait(function(rows) {
          if (rows.length == 1 && rows[0].uid == user_id) {
            console.log("LIKE");
            $('#container_notlike').hide();
            $('#container_like').show();
            gotoQR();
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
                gotoQR();
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
  <script type="text/javascript" src="jquery-1.10.2.min.js"></script>
</head>
<body>
  <?php if ($user) { ?>
  Your user profile is
  <pre>
    <?php print htmlspecialchars(print_r($user_profile, true)) ?>
  </pre>
  <pre>
    
    <a 
    <?php print htmlspecialchars(print_r($facebook->getAccessToken(), true)) ?>
  </pre>
  <fb:like href="http://www.facebook.com/Privateplace" show_faces="true" width="450"></fb:like>
  <?php } else { ?>
  <fb:login-button></fb:login-button>
  <?php } ?>
  <div id="fb-root"></div>
  <div id="container_notlike">
    YOU DON'T LIKE ME :(
  </div>
  <div id="container_like">
    YOU LIKE ME :)
  </div>
</body>
</html>
