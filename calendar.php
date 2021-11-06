<!DOCTYPE html>
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title>Arkux</title>
		<link rel="shortcut icon" href="Arkux.ico" type="image/x-icon" />
        <link href="http://fonts.cdnfonts.com/css/common-pixel" rel="stylesheet">
        <link href="http://fonts.cdnfonts.com/css/phatone" rel="stylesheet">
        <link href="http://fonts.cdnfonts.com/css/newtieng" rel="stylesheet">
        <link href="calStylesheet.css" rel="stylesheet" media="screen" type="text/css">
    </head>
<body style="background-color:black;">

<hr color="#67E4F9">
<table border="0" width="100%"><tr>
    <td ><div class="title">Calendar</div></td>
</tr></table>
<hr color="#67E4F9">

<?php
    include "../calHeader.php";
	?>
    <?php
        $NOW = array(date("Y"),date("n"),date("d"));

    ?>

    <table border="1" cellspacing="9" bordercolor="black" align="center" bgcolor="black">    
    <?php
        $currentMonth = New Month($NOW[0],$NOW[1]);
        $currentMonth->displayMonth($NOW[2],1);
        $m = $currentMonth->getAdjMonths();
        $nextMonth = New Month($m[0],$m[1]);
        $nextMonth->displayMonth(0,0);
    ?>
    </tr></table>
    <i>Updated at <?php echo date("Y-m-d H:i:s"), " (GMT+8)"; ?></i>
	</body>
</html>