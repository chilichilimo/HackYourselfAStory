<?php
  //
  // Database config
  //
  require(dirname(__FILE__) . "/../conf/dbpasswd.php"); // Include the database password from another file
  define("DB_HOST", "localhost");
  define("DB_USERNAME", "hackastory");
  define("DB_DATABASE", "hackyourselfastory");

  //
  // Pusher config
  //
  require(dirname(__FILE__) . "/../conf/pushersecret.php"); // Include the Pusher secret from another file
  define("PUSHER_APP_ID", "183533");
  define("PUSHER_APP_KEY", "7d0b4730386735df8793");
  define("PUSHER_CHANNEL", "private-threewords");
?>
