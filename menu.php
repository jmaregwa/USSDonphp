<?php

$menu_dlight = array(
    "1" => array("title" => "Welcome to company\r\n1. Check for D.light tokens \r\n2. Register Product for warranty \r\n3. Buy Tokens.\r\n4. Balance Enquiry.\r\n5. Ask for a call center call.\r\n 0 Main Menu 99 Back", "minValue" => 1, "maxValue" => 9, "datatype" => 'int', 'input' => 1, "type" => 'CON', 'level' => 0),

    //Check d.light tokens
      "1*1" => array("title" => "Please enter your company Account Number \r\n0 Main Menu 99 Back", "minValue" => 2, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
      "1*1*0" => array("title" => "functionCall", 'function' => 'getdlightTokens', "minValue" => 1, "maxValue" => 60, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int'),
    //Warranty Registration
      
      "1*2" => array("title" => "Enter Your Name \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
    "1*2*0" => array("title" => "Enter Product Name \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
    "1*2*0*0" => array("title" => "Enter Serial Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
  
    "1*2*0*0*0" =>array("title" => "Enter ID Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
    "1*2*0*0*0*0" => array("title" => "Enter Your phone Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
    "1*2*0*0*0*0*0" => array("title" => "functionCall", 'function' => 'getwarrantyreg', "minValue" => 1, "maxValue" => 60, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int'),

  
//Buy Token
    "1*3" => array("title" => "Enter company Account Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),

    "1*3*0" => array("title" => " Enter Amount \r\n0 Main Menu 99 Back", "minValue" => 2, "maxValue" => 500000, "type" => 'string', "type" => 'con', 'input' => 0, 'datatype' => 'string'),
    "1*3*0*0" => array("title" => "functionCall", 'function' => 'buytoken', "minValue" => 1, "maxValue" => 60, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int'),

    //Check Balance
    "1*4" => array("title" => "Enter company Account Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),

    
    "1*4*0" => array("title" => "functionCall", 'function' => 'getdlightBalance', "minValue" => 1, "maxValue" => 60, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int'),


    //Request Call back
    "1*5" => array("title" => "Enter company Account Number \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
     "1*5*0" => array("title" => "Enter brief Description \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 100, "datatype" => 'string', "type" => 'CON', 'input' => 0),
 
    "1*5*0*0" => array("title" => "functionCall", 'function' => 'getdlightCallback', "minValue" => 1, "maxValue" => 60, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int'),
    
   
   
    //Change PIN
  /*  "1*3*4" => array("title" => "Enter your current PIN \r\n0 Main Menu 99 Back", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
    "1*3*4*0" => array("title" => "Enter new PIN", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
    "1*3*4*0*0" => array("title" => "Re-enter your new PIN", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
    "1*3*4*0*0*0" => array("title" => "functionCall", 'function' => 'changePinOkoa', "minValue" => 1, "maxValue" =>3, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int','level' => 0),*/



    );
$menu_Registration = array(
    "1" => array("title" => "Welcome to company Solution\r\n 1.Register\r\n2.Cancel \r\n0 Main Menu 99 Back", "minValue" => 1, "maxValue" => 2, "datatype" => 'int', 'input' => 1, "type" => 'CON', 'level' => 0),
    "1*1" => array("title" => "Enter Business Name \r\n0 Main Menu 99 Back",  "minValue" => 4, "maxValue" => 20, "datatype" => 'string', "type" => 'CON', 'input' => 0),
    "1*1*0" => array("title" => "Enter Agent Number  \r\n0 Main Menu 99 Back", "minValue" => 2, "maxValue" => 6, "datatype" => 'string', "type" => 'CON', 'input' => 0),
   
      "1*1*0*0" => array("title" => "functionCall",  "type" => 'CON', 'function' =>'registerMember', 'level' => 7),
    "1*2" => array("title" => " Registration successful.\r\nThank you for using Okoa Float Solution \r\n0 Main Menu 99 Back",  "type" => 'END')
);
$menu_Reset_Pin = array(
  "1" => array("title" => "First Time Login Pin Change\r\n 1.Continue\r\n2.Cancel \r\n0 Main Menu 99 Back", "minValue" => 1, "maxValue" => 2, "datatype" => 'int', 'input' => 1, "type" => 'CON', 'level' => 0),
  "1*2" =>  array("title" => "Thank you for using Company Solution \r\n0 Main Menu 99 Back",  "type" => 'END'),
  "1*1" => array("title" => "Enter Current Pin", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
  "1*1*0" => array("title" => "Enter new PIN", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
  "1*1*0*0" => array("title" => "Re-enter your new PIN", "minValue" => 4, "maxValue" => 4, "type" => 'string', "type" => 'CON', 'input' => 0, 'datatype' => 'string'),
  "1*1*0*0*0" => array("title" => "functionCall", 'function' => 'changePinOkoa', "minValue" => 1, "maxValue" =>3, 'input' => 0, "type" => 'int', "type" => 'CON', 'datatype' => 'int','level' => 0),
);

