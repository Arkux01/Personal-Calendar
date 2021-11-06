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
    <td ><a class="link" href="calendar.php"><< Back to Calendar</a></td>
    <td ><div class="title">- Scheduling Mode -</div></td>
</tr></table>
<hr color="#67E4F9">

<?php

include "../calHeader.php";

$Y=$_GET["Y"]??date("Y");
$M=$_GET["M"]??date("n");
$D=$_GET["D"]??date("d");
$edit = New Month($Y, $M);
$proceed = $edit->proceed($_GET,$_POST);
$status="";
if($proceed!=0){
    $status.="<div class='status".(($proceed>0)?"Success":"Failure")."'>";
    switch($proceed){
        case -1:
            $status.= "Invalid input: incorrect verification code. ";
        break;
        case -3:
            $status.= "Invalid input: blank entry. ";
        break;
        case -4:
            $status.= "Invalid input: illegal entry. ";
        break;
        case 1:
            $status.= "Change saved. ";
        break;
    }
    $status.="</div>";
}
echo '<table border="1" cellspacing="7" bordercolor="black" align="center" bgcolor="black">';

$edit -> displayMonth(1,2,$D);
?>

<tr><td colspan="7"><hr></td></tr>
<tr><td colspan="7" align="left">
    <?= '<div class="text"> - '.$D.' '.$edit->getMonthString().' - </div>'?>
    <form action='eventEdit.php?Y=<?=$Y?>&M=<?=$M?>&D=<?=$D?>' method='post'>
        <label class="container">
        <input type="radio" name="mode" id="add" value="add" checked="checked">
        <span class="checkmark" >Schedule a new 
            <select id='type' name='type' class="optionList" >
                <option value="0">event</option>
                <option value="1">deadline</option>

            </select>
         at
        </span> </label>
        <input type="time" class="time" name="time" value="23:59">

        <input type="text" class="content" name="event" maxlength="15">          
        
        <br>
        
        
        <?php if(isset($edit->days[$D])){ 
        //var_export($edit->days[$D]->events);

        echo '<label class="container">
        <input type="radio" name="mode" id="move" value="move" >
        <span class="checkmark" >';
        echo '<select id="mod" name="mod" class="optionList">
                <option value="0">Move</option>
                <option value="1">Duplicate</option>
            </select>';

        echo ' the entry ';

        echo '<select id="selection1" name="selection1" class="optionList">';

        foreach($edit->days[$D]->events as $i=>$e){
            echo '<option value="'.$i.'">'.$e->getTime().' '.$e->getContent().'</option>';
        }
        echo '</select> to ';

        echo '<select id="date" name="date" class="optionList">';
        for($i=1;$i<=$edit->numOfDays();$i++){
            if($i==$D)continue;
            echo '<option value="'.$i.'">'.$i.' '.$edit->getMonthString().'</option>';
        }
        echo '</select>';
        echo '</span> </label> <br>';


        echo '<label class="container">
        <input type="radio" name="mode" id="cross" value="cross" >
        <span class="checkmark" >Mark the entry ';

        echo '<select id="selection" name="selection" class="optionList">';

        foreach($edit->days[$D]->events as $i=>$e){
            echo '<option value="'.$i.'">'.$e->getTime().' '.$e->getContent().'</option>';
        }
        echo '</select>';
        echo ' as ';
        echo '<select id="change" name="change" class="optionList">
                <option value="9">done</option>
                <option value="5">important</option>
                <option value="2">event</option>
                <option value="1">deadline</option>
            </select>';
        echo '</span> </label><br>';

        echo '<label class="container">
        <input type="radio" name="mode" id="delete" value="delete" >
        <span class="checkmark" >Delete the entry ';
        echo '<select id="selection2" name="selection2" class="optionList">';

        foreach($edit->days[$D]->events as $i=>$e){
            echo '<option value="'.$i.'">'.$e->getTime().' '.$e->getContent().'</option>';
        }
        echo '</select>';
        echo '</span> </label>';

        } else {
           /* echo "<pre>";
            var_export($edit->days);
            echo "</pre>";*/
        }
        ?>

        <div align="right">
        <?=$status?>
        <input type="password" class="code" name="pw" id="pw" placeholder="- User Verification -">
        
        <input type="submit" class="submit" value=">>> Proceed >>>"></div>

    </form>
</td></tr>
<tr><td colspan="7"><hr></td></tr>
</table>

</body></html>