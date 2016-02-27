<?php
  require(dirname(__FILE__) . "/inc/config.inc.php");
  require( dirname(__FILE__) . "/inc/pusher/lib/Pusher.php");

  $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

  $pusher = new Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    array('encrypted' => true)
  );

  function SendToClients($event, $jsonData)
  {
    global $pusher;
    $pusher->trigger('threewords', $event, $jsonData);
  }

  function GetSentence()
  {
    global $mysqli;

    $result = $mysqli->query("SELECT starts.* FROM (SELECT FLOOR (RAND() *
      (SELECT count(*) FROM starts)) num ,@num:=@num+1 from (SELECT @num:=0)
      a , starts LIMIT 1) b ,  starts WHERE b.num=starts.id;")
        or trigger_error($mysqli->error);

    $data = $result->fetch_array(MYSQLI_ASSOC);

    return $data['text'];
  }

  while(1)
  {
    global $mysqli;
    $sentence = GetSentence();
    while(str_word_count($sentence) < 30)
    {
      SendToClients('new_phrase', "{'phrase': '" . $sentence . "'}");
      sleep(10);
      $request = $mysqli->query("SELECT * FROM suggestions");
      $suggestions = array("suggessions" => array());
      while ($row = $request->fetch_array(MYSQLI_ASSOC))
      {
        $suggestions['suggestions'][$row['id'] = $row['threewords'];
      }

      SendToClients('vote_request', json_encode($suggestions));
      sleep(10);

      $result = $mysqli->query("SELECT * FROM suggestions ORDER BY count
        DESC LIMIT 1");

      $top = $result->fetch_array(MYSQLI_ASSOC);

      $sentence .= " " .$top['threewords'];

      SendToClients("vote_result", json_encode(
        array(
          "sentence"    => $sentence,
          "winningtext" => $top['threewords']
            )
          )
        );

      sleep(5);
    }
  }


?>
