<?php 



/*

http://developers.facebook.com/setup/done?id=144934438868111&locale=en_US





Facebook DevelopersDocumentationForumShowcaseBlog

	

Create an Application

Settings



Twextra is now registered with Facebook. You can edit your application settings at any time in your Developer Dashboard.

App Name:	Twextra

App URL:	http://twextra.com/

App ID:	144934438868111

App Secret:	7fbd7dd5e754d201e3e59ee06fca7887

Sample Code



<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml"

      xmlns:fb="http://www.facebook.com/2008/fbml">

  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <title>Twextra</title>

  </head>

  <body>

    <h1>Twextra</h1>

    <p><fb:login-button autologoutlink="true"></fb:login-button></p>

    <p><fb:like></fb:like></p>



    <div id="fb-root"></div>

    <script>

      window.fbAsyncInit = function() {

        FB.init({appId: '144934438868111', status: true, cookie: true,

                 xfbml: true});

      };

      (function() {

        var e = document.createElement('script');

        e.type = 'text/javascript';

        e.src = document.location.protocol +

          '//connect.facebook.net/en_US/all.js';

        e.async = true;

        document.getElementById('fb-root').appendChild(e);

      }());

    </script>

  </body>

</html>



Next Steps



You can add more cut-and-paste social functionality to your site with social plugins like the Like button in the sample above.



To incorporate the active user's profile and friends into your server-side code, you should use the Graph API. We support a number of SDKs to make that process easier:



    * PHP SDK

    * Python SDK

    * iPhone SDK



Check out the getting started guide for more information.

Facebook © 2010

AboutPrinciples & PoliciesPrivacy Policy



*/



?>