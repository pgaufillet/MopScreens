<?php
  /*
  Copyright 2014 Metraware
  
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

  
  session_start();
  //date_default_timezone_set('Europe/Paris');
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();
  
  include_once('lang.php');
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Config Edit</title>
        <script type="text/javascript">

            function GoBack(rcid,sid,ret)
            {
              if (ret==1)
              {
                location.replace("screen.php?rcid="+rcid);
              }
              else
              {
                location.replace("screenedit.php?rcid="+rcid+"&sid="+sid);
              }
            }        

            function Take()
            {
              var availlist = document.getElementById("availablecourses");
              var takenlist = document.getElementById("takencourses");
              if (availlist.multiple == false)
              {
                var id = availlist.options[availlist.selectedIndex].value;
                var txt = availlist.options[availlist.selectedIndex].text;
                takenlist.options[takenlist.options.length] = new Option(txt, id);
                availlist.remove(availlist.selectedIndex);
              }
              else
              {
                var n=availlist.options.length;
              	for(var i=0; i<n; i++)
              	{
              		if(availlist.options[i].selected == true)
              		{
                    var id = availlist.options[i].value;
                    var txt = availlist.options[i].text;
                    takenlist.options[takenlist.options.length] = new Option(txt, id);
              		}
              	}
              	for(var i=n-1; i>=0; i--)
              	{
              		if(availlist.options[i].selected == true)
              		{
                    availlist.remove(i);
                  }
              	}
              }                
            }
            
            function GiveBack()
            {
              var availlist = document.getElementById("availablecourses");
              var takenlist = document.getElementById("takencourses");
              if (takenlist.multiple == false)
              {
                var id = takenlist.options[takenlist.selectedIndex].value;
                var txt = takenlist.options[takenlist.selectedIndex].text;
                availlist.options[availlist.options.length] = new Option(txt, id);
                takenlist.remove(takenlist.selectedIndex);
              }
              else
              {
                var n=takenlist.options.length;
              	for(var i=0; i<n ; i++)
              	{
              	  if(takenlist.options[i].selected == true)
              	  {
                    var id = takenlist.options[i].value;
                    var txt = takenlist.options[i].text;
                    availlist.options[availlist.options.length] = new Option(txt, id);
              	  }
              	}
              	for(var i=n-1; i>=0; i--)
              	{
              	  if(takenlist.options[i].selected == true)
              	  {
                    takenlist.remove(i);
                  }
                }
              }                
            }
            
            function Validate(rcid,cid,sid,panel,ret)
            {
              var str="";
              var takenlist = document.getElementById("takencourses");
              var n=takenlist.options.length;
              if (n>0)
              {
              	for(var i=0; i<n ; i++)
               	{
                  var id = takenlist.options[i].value;
                  str = str+"&selcourses[]="+id.toString();
              	}
                if (ret==1)
                {
                  location.replace("screen.php?action=updatecourses&rcid="+rcid+"&cid="+cid+"&sid="+sid+"&panel="+panel+str);
                }
                else
                {
                  location.replace("screenedit.php?action=updatecourses&rcid="+rcid+"&cid="+cid+"&sid="+sid+"&panel="+panel+str);
                }
              }
              else
              {
                if (ret==1)
                {
                  location.replace("screen.php?action=clearcourses&rcid="+rcid+"&cid="+cid+"&sid="+sid+"&panel="+panel);
                }
                else
                {
                  location.replace("screenedit.php?action=clearcourses&rcid="+rcid+"&cid="+cid+"&sid="+sid+"&panel="+panel);
                }
              }
            }        


        </script>
    </head>
    <body>
<?php

  include_once('screenfunctions.php');

  
  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();

  $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
  $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
  $sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
  $panel = isset($_GET['panel']) ? intval($_GET['panel']) : 0;
  $ret = isset($_GET['ret']) ? intval($_GET['ret']) : 0;
  if (($rcid>0)&&($cid>0)&&($sid>0)&&($panel>0))
  {
    // list of courses for this competition
    $availablecourselist= array();
    $sql = "SELECT id,name FROM mopcourse WHERE cid=$cid";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0)
    {
      while($r = mysqli_fetch_array($res))
      {
        $idtmp = $r['id'];
        $nametmp = $r['name'];
        $availablecourselist[$idtmp]=$nametmp;                
      }

      $takencourselist= array();
      $sqltmp = "SELECT resultcourse.id, name FROM resultcourse, mopcourse WHERE mopcourse.cid=resultcourse.cid AND mopcourse.id=resultcourse.id AND mopcourse.cid=$cid AND resultcourse.rcid=$rcid AND resultcourse.panel=$panel AND resultcourse.sid=$sid";
      $restmp = mysqli_query($link, $sqltmp);
      if (mysqli_num_rows($restmp) > 0)
      {
        while ($rtmp = mysqli_fetch_array($restmp))
        {
          $nametmp=$rtmp['name'];
          $idtmp=$rtmp['id'];
          $takencourselist[$idtmp]=$nametmp;                
        }
      }

      print "<table>\n";
      print "<tr>\n";
      print "<th>".MyGetText(50)."</th>\n"; // Displayed
      print "<th>&nbsp;</th>\n";
      print "<th>".MyGetText(51)."</th>\n"; // Available   
      print "</tr>\n";

      print "<tr>\n";
      print "<td rowspan=3><select name='takencourses' id='takencourses' size=40 multiple='multiple'>\n";
      foreach ($takencourselist as $takenid => $takenname)
      {
        print "<option value=$takenid>$takenname</option>\n";
        unset ($availablecourselist["$takenid"]);
      }
      print "</select></td>\n";
      print "<td>&nbsp;</td>\n";
      print "<td rowspan=3><select name='availablecourses' id='availablecourses' size=40 multiple='multiple'>\n";
      foreach ($availablecourselist as $availid => $availname)
      {
        print "<option value=$availid>$availname</option>\n";
      }
      print "</select></td>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "<td><input type='button' value='<' onclick='Take();'></td>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "<td><input type='button' value='>' onclick='GiveBack();'></td>\n";
      print "</tr>\n";
      print "</table>\n";
      
      print "<input type='button' value='".MyGetText(52)."' onclick='Validate($rcid,$cid,$sid,$panel,$ret);'>"; // OK
    }
  }
  print "<input type='button' value='".MyGetText(53)."' onclick='GoBack($rcid,$sid,$ret);'>"; // cancel
    
?>
    </body>
</html>
