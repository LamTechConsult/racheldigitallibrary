<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Prince of Wales School Digital Library by LAMTECH using RACHEL Content - HOME</title>
<link rel="stylesheet" href="css/normalize-1.1.3.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
<!--[if IE]><script type="text/javascript" src="css3-multi-column.min.js"></script><![endif]-->
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script>
    // this sets the autocomplete handler for each module's text input field
    $(document).ready( function () {
        $(":text").each( function () {
            var myid = $(this).attr("id");
            if (myid) {
                var moddir = myid.replace(/_search$/, "");
                $("#"+myid).autocomplete({
                        source: "modules/"+moddir+"/search/suggest.php",
                });
            }
        });
    });
</script>
</head>

<body>
<div id="rachel" style="position: relative;">
   <div class="mainheader">LAMTECH Digital Library</div> 
    <div id="ip">
    <?php
        # some notes to prevent future regression:
        # the PHP suggested gethostbyname(gethostname())
        # brings back the unhelpful 127.0.0.1 on RPi systems,
        # as well as slowing down some Windows installations
        # with a DNS lookup. $_SERVER["SERVER_ADDR"] will just
        # display what's in the user's address bar, so also
        # not useful - using ifconfig/ipconfig is probably
        # the way to go, but may require some tweaking
        echo "<b>Server Address</b><br>\n";
        if (preg_match("/^win/i", PHP_OS)) {
            # under windows it's ipconfig
            $output = shell_exec("ipconfig");
            preg_match("/IPv4 Address.+?: (.+)/", $output, $match);
            if (isset($match[1])) { echo "$match[1]<br>\n"; }
        } else if (preg_match("/^darwin/i", PHP_OS)) {
            # OSX is unix, but it's a little different
            exec("/sbin/ifconfig", $output);
            preg_match("/en0.+?inet (.+?) /", join("", $output), $match);
            if (isset($match[1])) { echo "$match[1]<br>\n"; }
        } else {
            # most likely linux based - so ifconfig should work
            exec("/sbin/ifconfig", $output);
            preg_match("/eth0.+?inet addr:(.+?) /", join("", $output), $match);
            if (isset($match[1])) { echo "LAN: $match[1]<br>\n"; }
            preg_match("/wlan0.+?inet addr:(.+?) /", join("", $output), $match);
            if (isset($match[1])) { echo "WIFI: $match[1]<br>\n"; }
        }
    ?>
    <a href="admin.php" style="position: absolute; font-size: small; bottom: 6px; right: 8px; color: #999;">reorder content</a>
    </div>
</div>

<div class="menubar cf">
    <ul>
    <li><a href="index.php">Home</a></li>
   <li><a href="http://192.168.88.1:8090/#/" target="blank">Content Hub</a></li>
<li><a href="http://192.168.88.1:8008/" target="blank">KA-Lite</a></li>
<li><a href="http://192.168.88.1:81/wikipedia_en_all_2015-05/?" target="blank">Wikipedia</a></li>
<li><a href="/modules/PhET/index.html" target="blank">PhET</a></li>
<li><a href="/modules/powertyping/index.html" target="blank">Typing</a></li>  
<li><a href="http://www.portal.schools.edu.sl/" target="blank">Open School Portal</a></li>  
<li><a href="about.html">About</a></li> 
</ul>
    
</div>

<div id="content">

<?php
    require_once("common.php");
    
    $fsmods = getmods_fs();
    # if there were any modules found in the filesystem
    if ($fsmods) {
        # get a list from the databases (where the sorting
        # and visibility is stored)
        $dbmods = getmods_db();
        # populate the module list from the filesystem 
        # with the visibility/sorting info from the database
        foreach (array_keys($dbmods) as $moddir) {
            if (isset($fsmods[$moddir])) {
                $fsmods[$moddir]['position'] = $dbmods[$moddir]['position'];
                $fsmods[$moddir]['hidden'] = $dbmods[$moddir]['hidden'];
            }
        }
        # custom sorting function in common.php
        uasort($fsmods, 'bypos');
        # whether or not we were able to get anything
        # from the DB, we show what we found in the filesystem
        $modcount = 0;
        foreach (array_values($fsmods) as $mod) {
            if ($mod['hidden'] || $mod['nohtmlf']) { continue; }
            $dir  = $mod['dir'];
            $moddir  = $mod['moddir'];
            include "$mod[dir]/index.htmlf";
            ++$modcount;
        }
    }
    if ($modcount == 0) {
        echo "<h2>No modules found.</h2>\n";
        echo "Please check there are modules in the modules directory,\n";
        echo "and that they are not all hidden on the admin page.\n";
    }
?>

</div>

<div class="menubar cf" style="margin-bottom: 80px;">
    <ul>
     <li><a href="index.php">Home</a></li>
   <li><a href="http://192.168.88.1:8090/#/" target="blank">Content Hub</a></li>
<li><a href="http://192.168.88.1:8008/" target="blank">KA-Lite</a></li>
<li><a href="http://192.168.88.1:81/wikipedia_en_all_2015-05/?" target="blank">Wikipedia</a></li>
<li><a href="/modules/PhET/index.html" target="blank">PhET</a></li>
<li><a href="/modules/powertyping/index.html" target="blank">Typing</a></li>  
<li><a href="http://www.portal.schools.edu.sl/" target="blank">Open School Portal</a></li>  
<li><a href="about.html">About</a></li> 
    </ul>
    <div id="footer_right">RACHEL+ 1.1(beta)</div>
</div>

</body>
</html>
