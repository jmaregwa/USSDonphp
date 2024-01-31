<?php

require_once("classes/Database.php");
require_once("classes/menu.php");
require_once("classes/ussdsession.php");
require_once("classes/common.php");
require_once("classes/messages.php");


$msisdn = isset($_REQUEST['MSISDN']) ? $_REQUEST['MSISDN'] : "";
$sessionId = isset($_REQUEST['session_id']) ? $_REQUEST['session_id'] : "";
$ussdString = isset($_REQUEST['ussd_string']) ? $_REQUEST['ussd_string'] : "";

$common = new common($messages);
$session = new ussdsession();


//$pinchange=$common->getUserPinStatusOkoa($msisdn);
//$userRegStatus = $common->getUserStatusOkoa($msisdn);

//echo $pinchange;


if ($ussdString == "") {
    //clear user sessions
    $session->clearSessions($msisdn);
    $menu = "1";
    $sessionString = "1";
    $session->createSessions($sessionId, $msisdn, $menu, $sessionString);
     $menu = $menu_dlight["1"];

  /*  if (!$userRegStatus) {
        $menu = $menu_Registration["1"];
    } else {
        if(!$pinchange) {
            $menu = $menu_dlight["1"];
        } else {
            $menu = $menu_Reset_Pin["1"];
        }
    }*/


    if ($menu["title"] == "functionCall") {
        $response = call_user_func($menu["function"], $menu, $msisdn, $sessionId, $menu, $sessionString, false);
    } else {
        $response = $menu["type"] . " " . $menu["title"];
    }
} else {

    //short cuts
    echo $ussdString;
    $input = array_pop(explode("*", $ussdString));
    $savedMenuArr = $session->getSessionString($sessionId, $msisdn);


    $currentSavedMenu = $savedMenuArr[0];
    $currentUssdString = $savedMenuArr[1];

    if ($input == "99") {
        if (substr_count($currentSavedMenu, "*") >= 1) {
            $currentSavedMenuArr = explode("*", $currentSavedMenu);
            $currentUssdStringArr = explode("*", $currentUssdString);
            array_pop($currentSavedMenuArr);
            array_pop($currentUssdStringArr);
            $currentSavedMenu = implode("*", $currentSavedMenuArr);
            $currentUssdString = implode("*", $currentUssdStringArr);
        }

      
        $menu = $menu_dlight[$currentSavedMenu];

       /* if (!$userRegStatus) {
            $menu = $menu_Registration[$currentSavedMenu];
        } else {
            if(!$pinchange) {
                $menu = $menu_dlight[$currentSavedMenu];
            } else {
                $menu = $menu_Reset_Pin[$currentSavedMenu];
            }
        }*/

        if ($menu["title"] == "functionCall") {
            $response = call_user_func($menu["function"], $menu, $msisdn, $sessionId, $currentSavedMenu, $currentUssdString, false);
        } else {
            $response = $menu["type"] . " " . $menu["title"];
        }
        $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
    } elseif ($input == "0") {
        /**
         *  For Registered users leave the first two parts of the menu
         *  For the rest take them to 1 
         */
          
       /* if (!$userRegStatus) {
            $session->clearSessions($msisdn);
            $menu = "1";
            $sessionString = "1";
            $session->createSessions($sessionId, $msisdn, $menu, $sessionString);
            $menu = $menu_Registration["1"];
        } else {
            /* $currentSavedMenuArr = explode("*", $currentSavedMenu);
              $currentUssdStringArr = explode("*", $currentUssdString);


              array_splice($currentSavedMenuArr, 2);
              array_splice($currentUssdStringArr, 2);
              $currentSavedMenu = implode("*", $currentSavedMenuArr);
              $currentUssdString = implode("*", $currentUssdStringArr);

              $sessionString = $currentUssdString;
              $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
              $menu = $menu_dlight[$currentSavedMenu];
             */
            $session->clearSessions($msisdn);
            $menu = "1";
            $sessionString = "1";
            $session->createSessions($sessionId, $msisdn, $menu, $sessionString);
            $menu = $menu_dlight[$menu];

           /* if(!$pinchange){

                 $menu = $menu_dlight[$menu];
            } else {
                if(!$pinchange) {
                    $menu = $menu_dlight[$currentSavedMenu];
                } else {
                    $menu = $menu_Reset_Pin[$currentSavedMenu];
                }
            }
        }*/

        //


        if ($menu["title"] == "functionCall") {
            $response = call_user_func($menu["function"], $menu, $msisdn, $sessionId, $menu, $sessionString, false);
        } else {
            $response = $menu["type"] . " " . $menu["title"];
        }
    } else {

        $menu = $menu_dlight[$currentSavedMenu];
        /*
        if (!$userRegStatus) {
            $menu = $menu_Registration[$currentSavedMenu];
        } else {
            if(!$pinchange) {
                $menu = $menu_dlight[$currentSavedMenu];
            } else {
                $menu = $menu_Reset_Pin[$currentSavedMenu];
            }
        }*/

        $type = $menu['datatype'];
        $min = $menu['minValue'];
        $max = $menu['maxValue'];
        $status = false;

        switch ($type) {
            case "string":
                if (strlen($input) >= $min && strlen($input) <= $max) {
                    $status = true;
                }
                break;
            case "int":
                if ($input >= $min && $input <= $max)
                    $status = true;
                break;
            case "amount":
                if ($common->validateAmount($input)) {
                    #check user max on this account
                    #$max check user max this is the general max
                    #use linux to store this in session

                    if ($input >= $min && $input <= $max) {
                        $status = true;
                    }
                }
                break;
            case "msisdn":
                $mobile = $common->validateMsisdn(substr($input, -9));
                if ($mobile > 0) {
                    $input = "254" . substr($mobile, -9);
                    $status = true;
                }
                break;
        }

        if ($status) {
            //check if there is function handling this request
            if ($menu['input'] > 0) {
                $currentSavedMenu .= "*" . $input;
            } else {
                $currentSavedMenu .= "*0";
            }
            $currentUssdString .= "*" . $input;

             $menu = $menu_dlight[$currentSavedMenu];
           /* if (!$userRegStatus) {
                $menu = $menu_Registration[$currentSavedMenu];
            } else {
                if(!$pinchange) {
                    $menu = $menu_dlight[$currentSavedMenu];
                } else {
                    $menu = $menu_Reset_Pin[$currentSavedMenu];
                }
            }*/


            $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
            // echo $currentSavedMenu."<br/>".$currentUssdString."<br/>";
            //$response = $menu["type"] . "  " . $menu["title"];
            if ($menu["title"] == "functionCall") {
                $response = call_user_func($menu["function"], $menu, $msisdn, $sessionId, $currentSavedMenu, $currentUssdString, $status);
            } else {
                $response = $menu["type"] . " " . $menu["title"];
            }
        } else {
            if ($menu["title"] == "functionCall") {
                $response = call_user_func($menu["function"], $menu, $msisdn, $sessionId, $currentSavedMenu, $currentUssdString, $status);
            } else {
                $response = $menu["type"] . " " . $menu["title"];
            }
        }
    }
}

function getServiceMenu($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
   
    $message = "CON " . $common->getServiceMenuK();
    
    return $message;
}

function getVehicle($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->getVehicleMenu($msisdn);
    return $message;
}

function saveVehicle1($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->savevehicle($menuItem[0], $menuItem[1], $msisdn);
    $currentSavedMenuArr = explode("*", $currentMenu);
    $currentUssdStringArr = explode("*", $sessionString);
    // array_splice($currentSavedMenuArr, 5);
    //   array_splice($currentUssdStringArr, 5);

    $currentSavedMenu = implode("*", $currentSavedMenuArr);
    $currentUssdString = implode("*", $currentUssdStringArr);
    $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
    return $message;
}

function saveVehicle($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->savevehicle($menuItem[0], $menuItem[1], $msisdn);
    $currentSavedMenuArr = explode("*", $currentMenu);
    $currentUssdStringArr = explode("*", $sessionString);
    array_splice($currentSavedMenuArr, 5);
    array_splice($currentUssdStringArr, 5);

    $currentSavedMenu = implode("*", $currentSavedMenuArr);
    $currentUssdString = implode("*", $currentUssdStringArr);
    $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
    return $message;
}

function deleteVehicle($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->deletevehicle($menuItem[0], $msisdn);
    return $message;
}

function getProductMenu($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    //check is service available

    $menuItem = array_reverse(explode("*", $sessionString));
   
        //get product using service selection
    //if($menuItem[0]==4){
      // $message = "CON " . $common->getProductCess($menuItem[0]); 
   // } else{
        $message = "CON " . $common->getProductMenu1($menuItem[0]);
   // }
    return $message;
}
function getCessMenu($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    //check is service available

    $menuItem = array_reverse(explode("*", $sessionString));
   
        //get product using service selection
    
        $message = "CON " . $common->getProductMenuCess($menuItem[0]);
    
    return $message;
}
function getProductMenuOrg($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    //check is service available

    $menuItem = array_reverse(explode("*", $sessionString));
    if (!$common->confirmService($menuItem[1])) {
        //get product using srrvice and subcountyid
        $message = "CON " . $common->getProductMenu($menuItem[0], $menuItem[1]);
    } else {
        $currentSavedMenuArr = explode("*", $currentMenu);
        $currentUssdStringArr = explode("*", $sessionString);
        array_pop($currentSavedMenuArr);
        array_pop($currentUssdStringArr);
        $currentSavedMenu = implode("*", $currentSavedMenuArr);
        $currentUssdString = implode("*", $currentUssdStringArr);
        $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);

        $message = "CON Invalid Service\r\n" . $common->getServiceMenu($menuItem[1]);
    }

    return $message;
}
function getvehicleTypes($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->getvehicleType();
    return $message;
}
function getdlightTokens($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
   // $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON The last three tokens are:\r\n 1. 1254678\r\n 2.4521367 \r\n 3. 1234785\r\n 00. Main Menu";
    return $message;
}
function getwarrantyreg($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON Thank you for buying a d.light".$menuItem[3]." 0. Main Menu";
    return $message;
}
function buytoken($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON Transaction of KSH : ".$menuItem[0]."is being Processed.We will notify you once complete.\r\n  0. Main Menu";
    return $message;
}

function getdlightBalance($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
   // $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON You have a current balance of 100.\r\n Thank you for using our Services.\r\n  0. Main Menu";
    return $message;
}
function getdlightCallback($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common;
   $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON We have received your call back request on: ".$menuItem[0].".\r\n Our Customer service Team will call you back.\r\nThank you for using our Services.\r\n  0. Main Menu";
    return $message;
}

function confirmTrailerPayment($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $messages, $common;
    $message = "END Error occured during processing";
    $paymentDetails = array_reverse(explode("*", $sessionString));
    $amount = $common->toMoney($mpesa_details[0]);
    $place_holders = array('^AMOUNT^', '^MSISDN^');
    $values = array($amount, $msisdn);
    $message = "CON " . str_replace($place_holders, $values, $messages['AIRTIME_CONFIRM']);


    return $message;
}

function getWalletBalanceorg($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;
    $walletPIN = array_reverse(explode("*", $sessionString));
    if (count($walletPIN) >= $menu['level']) {
        if ($common->checkUserPIN($msisdn, $walletPIN[0])) {
            $balance = $common->checkWalletBallance($msisdn);
            $message = "CON Your wallet balance is KSH. " . $balance . " as at " . date("Y-m-d") . ". Thank you for using Laikipia County Council E-Payment System.";
        } else {
            $message = "CON You entered an invalid PIN. Please try again later.";
        }
    } else {
        $message = "END Error retrieving your wallet balance. Please try again later.";
    }
    return $message;
}

function getWalletBalance($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;

    $balance = $common->checkWalletBallance($msisdn);
    $message = "CON Your wallet balance is KSH. " . $balance . " as at " . date("Y-m-d") . ". Thank you for using Busia County Council E-Payment System.";


    //$message = "END Error retrieving your wallet balance. Please try again later.";
    return $message;
}

function confirmMpesa($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON Confirm Mpesa Top up of KES:" . $menuItem[0] . "  \r\n1. Confirm\r\n 2. Cancel \r\n";
    return $message;
}

function confirmVehicleNumber($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {


    global $common;

    // $message = "CON " . $common->getVehicle($msisdn);
    //return $message;
    $menuItem = array_reverse(explode("*", $sessionString));


        $message = "CON " . $common->getVehicleMenuHB($menuItem[1], $menuItem[2], $msisdn);
    
    return $message;

    //$message = "END Error retrieving your wallet balance. Please try again later.";
}

function confirmVehicleNumber1($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {


    global $common;

    // $message = "CON " . $common->getVehicle($msisdn);
    //return $message;
    $menuItem = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->getVehicleMenu1($msisdn);
    return $message;

    //$message = "END Error retrieving your wallet balance. Please try again later.";
}

function sessionhijack($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $accounts, $userDetails, $sessionId, $messages, $session;
    $depositDetails = array_reverse(explode("*", $sessionString));

    #add Hijack to the sesison
    #$currentSavedMenu =  $currentMenu . "*Hijack";
    #$currentUssdString = $sessionString . "*Hijack";
    #$session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
    $message = "UPR ";
    #que request to call C2B Wrapper
    return $message;
}

function getStatementorg($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;
    $walletPIN = array_reverse(explode("*", $sessionString));
    if (count($walletPIN) >= $menu['level']) {
        if ($common->checkUserPIN($msisdn, $walletPIN[0])) {
            $message = "CON " . $common->checkStatement($msisdn);
        } else {
            $message = "CON You entered an invalid PIN. Please try again later.";
        }
    } else {
        $message = "CON Error retrieving your wallet statement. Please try again later.";
    }
    return $message;
}

function depositViaMpesa($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common, $sessionId, $accounts;
    $depositDetails = array_reverse(explode("*", $sessionString));
    $message = "CON " . $common->depositFunds($sessionId, $msisdn, $depositDetails[2]);
    #que request to call C2B Wrapper

    return $message;
}

function getStatement($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;
    $walletPIN = array_reverse(explode("*", $sessionString));

    $ministatement = $message = $common->checkStatement($msisdn);
    $message = "CON Your Wallet Mini-Statement is" . $ministatement . " Thank you for using Busia E payment";


    return $message;
}

function changePin($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common, $menu_Registed, $session;
    $memberDetails = array_reverse(explode("*", $sessionString));
    if (count($memberDetails) >= $menu['level']) {
        if ($common->checkUserPIN($msisdn, $memberDetails[2])) {
            if ($memberDetails[0] <> $memberDetails[1]) {
                //go back to previous menu
                $currentSavedMenuArr = explode("*", $currentMenu);
                $currentUssdStringArr = explode("*", $sessionString);
                array_pop($currentSavedMenuArr);
                array_pop($currentUssdStringArr);
                $currentSavedMenu = implode("*", $currentSavedMenuArr);
                $currentUssdString = implode("*", $currentUssdStringArr);


                $message = $menu["type"] . " Invalid PIN. " . $menu["title"];
                $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
            } else {
                $message = "CON " . $common->changePIN($msisdn, $memberDetails[2], $memberDetails[0]);
            }
        } else {
            $message = "CON You entered an invalid PIN. Please try again later.";
        }
    } else {
        $message = "CON Error changing your password. Please try again later.";
    }
    return $message;
}

function changePinOkoa($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common, $menu_Registed, $session;
    $memberDetails = array_reverse(explode("*", $sessionString));
    if (count($memberDetails) >= $menu['level']) {
        $message = "CON " . $common->changePINokoa($msisdn, $memberDetails[2], $memberDetails[0]);
            
    } else {
        $message = "CON Error changing your password. Please try again later.";
    }
    return $message;
}

function registerMember($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common, $session, $userRegStatus, $menu_Registration;
    $memberDetails = array_reverse(explode("*", $sessionString));
    
        /*if ($memberDetails[0] <> $memberDetails[1]) {
            //go back to previous menu
            $currentSavedMenuArr = explode("*", $currentMenu);
            $currentUssdStringArr = explode("*", $sessionString);
            array_pop($currentSavedMenuArr);
            array_pop($currentUssdStringArr);
            $currentSavedMenu = implode("*", $currentSavedMenuArr);
            $currentUssdString = implode("*", $currentUssdStringArr);

            $message = $menu["type"] . " Your confirm PIN is invalid. Re enter PIN";
            $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
        } else {*/
            $bname = $memberDetails[1];
            $agentnumber = $memberDetails[0];
           // $pin = $memberDetails[1];
            $message = $common->registerUserokoa($msisdn, $bname, $agentnumber);
            
       // }
    
    return $message;
}

function getministatement($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting the ministatement";
    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $pin=$walletPIN[0];
        $message = $common->reguestStatement($msisdn,$pin);
   
    return $message;

}
function getloanLimit($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting the Limit";
    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $pin=$walletPIN[0];
        $message = $common->reguestLimit($msisdn,$pin);
   
    return $message;

}
function getOkoa($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting Okoa";
    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $pin=$walletPIN[0];
    $amount=$walletPIN[2];
    //echo $pin.$walletPIN[2];
   // die();
   
        $message = $common->reguestOkoa($msisdn,$amount,$pin);
    
    return $message;

}
function payOkoa($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting Okoa";
    global $common, $session,$amount;
    $result= array_reverse(explode("*", $sessionString));
   // $pin=$walletPIN[3];
    $amount=$result[2];
    //echo $pin.$walletPIN[2];
   // die();

        $message = $common->repayOkoa($msisdn,$amount,$sessionId);
    
    return $message;

}
function makeOkoa($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting Okoa";
    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $pin=$walletPIN[0];
    
    //echo $pin.$walletPIN[2];
   // die();
   
        $message = $common->makepaymentokoa($msisdn,$pin);

   
    
   
    return $message;

}
function getloanBalance($menu, $msisdn, $sessionId, $currentMenu, $sessionString){
    //echo "getting the Balance";
    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $pin=$walletPIN[0];
   // echo $pin.$walletPIN[0];
   // die();
    
        $message = $common->reguestBalance($msisdn,$pin);
   
    return $message;

}
function getloanRequest($menu, $msisdn, $sessionId, $currentMenu, $sessionString){

    global $common, $session,$amount;
    $walletPIN = array_reverse(explode("*", $sessionString));
    $amount=$walletPIN[1];
    //echo $amount.$walletPIN[0];
    //die();
    if ($common->checkUserPIN($msisdn, $walletPIN[0])) {

        // $message = "CON Welcome E-Payment Account. Welcome \r\n1. My Account\r\n2. Transaction";
        $message = $common->reguestloan($msisdn,$amount);
    } else {
        $message = "CON You entered an invalid PIN. Please try again later.\r\n0 Main Menu 99 Back";
    }
    return $message;

}
function registerMemberorg($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common, $session, $userRegStatus, $menu_Registration;
    $memberDetails = array_reverse(explode("*", $sessionString));
    if (count($memberDetails) >= $menu['level']) {
        if ($memberDetails[0] <> $memberDetails[1]) {
            //go back to previous menu
            $currentSavedMenuArr = explode("*", $currentMenu);
            $currentUssdStringArr = explode("*", $sessionString);
            array_pop($currentSavedMenuArr);
            array_pop($currentUssdStringArr);
            $currentSavedMenu = implode("*", $currentSavedMenuArr);
            $currentUssdString = implode("*", $currentUssdStringArr);

            $message = $menu["type"] . " Your confirm PIN is invalid. Re enter PIN";
            $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);
        } else {
            $fname = $memberDetails[4];
            $laname = $memberDetails[3];
            $nationalId = $memberDetails[2];
            $pin = $memberDetails[1];
            $result = $common->registerUser($msisdn, $fname, $laname, $nationalId, $pin);
            if ($result)
                $message = "END Welcome to Okoa Float Solution. Your registration was successful.";
            else
                $message = "END Error registering your details please try again later";
        }
    } else {
        $message = "END Error registering your details please try again later";
    }
    return $message;
}

function welcomeRegisteredMember($menu, $msisdn, $sessionId, $currentMenu, $sessionString) {
    global $common;
    $memberName = $common->getUserName($msisdn);
    $message = "CON Welcome " . $memberName . " to Okoa Float Solution\r\n1.My Account\r\n2.Market Cess\r\n3.Parking\r\n4.Business Permits\r\n5.Contact Us";
    return $message;
}

function confirmPayment($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    if ($menuItem[0] <= $common->getProductMenuCount($menuItem[1], $menuItem[2])) {
        //get product using srrvice and subcountyid
        $message = $common->canPay($menuItem[0], $menuItem[1], $menuItem[2], $msisdn);
    } else {
        $currentSavedMenuArr = explode("*", $currentMenu);
        $currentUssdStringArr = explode("*", $sessionString);
        array_pop($currentSavedMenuArr);
        array_pop($currentUssdStringArr);
        $currentSavedMenu = implode("*", $currentSavedMenuArr);
        $currentUssdString = implode("*", $currentUssdStringArr);
        $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);

        $message = "CON Invalid product\r\n\r\n0 Main Menu 99 Back" . $common->getProductMenu($menuItem[1], $menuItem[2]);
    }
    return $message;
}
function confirmPaymentHBM($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    
        $message = $common->canPay($menuItem[0], $menuItem[1], $menuItem[2], $msisdn);
    
    return $message;
}

function confirmPayment2($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    
        $message = $common->canPay($menuItem[1], $menuItem[2], $menuItem[3], $msisdn);
   
    return $message;
}

function loginWallet($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $walletPIN = array_reverse(explode("*", $sessionString));

    if ($common->checkUserPIN($msisdn, $walletPIN[0])) {
        //Welcome E-Payment Account. My Account \r\n1. Check Balance.\r\n2. Mini Statement.\r\n3. Top Up.\r\n4. Change PIN."
        $message = "CON Welcome E-Payment Account. My Account \r\n1. Check Balance.\r\n2. Mini Statement.\r\n3. Top Up.\r\n4. Change PIN.\r\n 5. Manage Vehicles";
    } else {
        $message = "CON You entered an invalid PIN. Please try again later.";
    }
    return $message;
}

function loginTrans($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $walletPIN = array_reverse(explode("*", $sessionString));

    if ($common->checkUserPIN($msisdn, $walletPIN[0])) {

        // $message = "CON Welcome E-Payment Account. Welcome \r\n1. My Account\r\n2. Transaction";
        $message = payment2($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status);
    } else {
        $message = "CON You entered an invalid PIN. Please try again later.\r\n0 Main Menu 99 Back";
    }
    return $message;
}

function loginTrans2($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $walletPIN = array_reverse(explode("*", $sessionString));

    if ($common->checkUserPIN($msisdn, $walletPIN[0])) {

        // $message = "CON Welcome E-Payment Account. Welcome \r\n1. My Account\r\n2. Transaction";
        $message = payment($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status);
    } else {
        $message = "CON You entered an invalid PIN. Please try again later.\r\n0 Main Menu 99 Back";
    }
    return $message;
}

function payment($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    $extra = ""; //Not used here
    $message = $common->payServiceHBM($menuItem[2], $menuItem[3],$menuItem[4],$msisdn);


    return $message;
}

function payment2($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    $menuItem = array_reverse(explode("*", $sessionString));
    //echo sizeof($menuItem);
    $message = $common->payServiceV($menuItem[3], $menuItem[4], $menuItem[5], $msisdn, $menuItem[2]);


    return $message;
}

function sendToMpesa($menu, $msisdn, $sessionId, $currentMenu, $sessionString, $status) {
    global $common, $session;
    //check is service available

    $menuItem = array_reverse(explode("*", $sessionString));
    if ($menuItem[0] <= $common->getProductMenuCount($menuItem[1], $menuItem[2])) {
        //get product using srrvice and subcountyid
        $message = "END Please wait as your transaction is being processed.";
    } else {
        $currentSavedMenuArr = explode("*", $currentMenu);
        $currentUssdStringArr = explode("*", $sessionString);
        array_pop($currentSavedMenuArr);
        array_pop($currentUssdStringArr);
        $currentSavedMenu = implode("*", $currentSavedMenuArr);
        $currentUssdString = implode("*", $currentUssdStringArr);
        $session->updateSessions($sessionId, $msisdn, $currentSavedMenu, $currentUssdString);

        $message = "CON Invalid product\r\n\r\n0 Main Menu 99 Back" . $common->getProductMenu($menuItem[1], $menuItem[2]);
    }

    return $message;
}

echo str_replace('con ', 'CON ', $response);
?>
