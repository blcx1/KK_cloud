<?php
namespace SAM;

/* ---------------------------------
    SAMMessage
   --------------------------------- */
class SAMMessage {

	var $body = "";	
  /* ---------------------------------
      Constructor
     --------------------------------- */
  function SAMMessage($body='') {

    if ($body != '') {
        $this->body = $body;
    }

  }
  
  function setMessage($msg){
  	$this->body = $msg;
  }
}
