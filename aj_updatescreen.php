<?php
  /*
  Copyright 2014-2015 Metraware
  
  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at
  
      http://www.apache.org/licenses/LICENSE-2.0
  
  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
  */
  

  include_once('functions.php');
  include_once('lang.php');
  session_start();
  
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  
  header('Content-type: text/html;charset=utf-8');

  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();
	
	$rcid = ((isset($_GET['rcid'])) ? intval($_GET['rcid']) : 0);
  $sid = ((isset($_GET['sid'])) ? intval($_GET['sid']) : 0);
	
	if(($rcid > 0) && ($sid > 0))
	{
    $now = time();
    $str = "refresh=$now";
    $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid AND sid=$sid";
    $res = mysqli_query($link, $sql);
    echo 'OK';
	}
  else
  {
    echo 'KO';
  }
