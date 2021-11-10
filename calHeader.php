<?php
    date_default_timezone_set("Asia/Hong_Kong");

class Event {

    private $h;
    private $m;
    private $content;
    protected $status;

    public function getTime(){
        return $this->h.":".$this->m;
    }
    public function getTime2(){
        return $this->h."::".$this->m;
    }

    public function getContent(){
        return $this->content;
    }

    protected function getStatus(){
        return $this->status;
    }
    
    protected function getMinutes(){
        return $this->h*60+$this->m;
    }


    protected function __construct($h, $m, $content,$status){
        $this->h=$h;
        $this->m=$m;
        $this->content=$content;
        $this->status=$status;
    }

}

class Day extends Event {
    private $day;
    /*private $weekday;*/
    public $events = []; //array of Event

    protected function __construct($day){
        $this->day=$day;
    }

    protected function getDay(){
        return $this->day;
    }

    protected function isBlank(){
        if(count($this->events)==0) return true;
        else {
            foreach ($this->events as $e){
                if ($e->getStatus()!=9) return false;
            }
        }
        return true;
    }

    protected function addEvent($h, $m, $content, $status){
        if($h > 23 || $h < 0 || $m > 59 || $m <0){ // Validity Check
           return; 
        }
        $insert = new Event($h, $m, $content, $status);
        $index = count($this->events);
        while($index){
            $prev=$this->events[$index-1];
            if($h*60+$m >= $prev->getMinutes())break;
            $this->events[$index]=$prev;
            $index--;
        }
        $this->events[$index] = $insert;
        return;
    }

    protected function deleteEvent($i){
        if($i<0 || $i >= count($this->events)){ // Validity Check
            return;
        }
        while($i < count($this->events)-1){
            $this->events[$i]=$this->events[$i+1];
            $i++;
        }
        unset($this->events[$i]);
    }

    protected function writeEvents(){
        $string = "";
        foreach($this->events as $v){
            $string .= $v->getTime2()."::".$v->getContent()."::".$v->getStatus().",, ";
        }
        return $string;
    }

    protected function readEvents($string){
        $string = explode(",, ", $string);
        echo array_pop($string);
        foreach($string as $s){
            $e = explode("::",$s);
            //var_export($e);
            $this->addEvent($e[0],$e[1],$e[2],$e[3]);
        }
        return;
    }

    protected function eventString($year, $month, $day, $mode=1){
        $today = [];
        foreach ($this->events as $e){
            $eventString="";
            switch($e->getStatus()){
                case '1': $style="Deadline" ; break;
                case '5': $style="Important"; break;
                case '9': $style="Cleared"; break;
                case '2': $style="Sched_1" ; break;
                case '3': $style="Sched_2" ; break;
                case '4': $style="Sched_3" ; break;
                case '6': $style="Sched_4" ; break;
                case '7': $style="Sched_5" ; break;
                case '0': $style="";
            }
            if($mode!=2 && strtotime($year."-".$month."-".$day)<strtotime(date("Y")."-".date("n")."-".date("d")))$style="Cleared";
            $eventString.="<span class='events".$style."'>";
            $eventString.=$e->getTime()." ".$e->getContent();
            $eventString.="</span>";
            $today[] = $eventString;   
        }
        return implode("<br>", $today);
    }
}

class Month extends Day {
    private $year;
    private $month;
    public $days=[];
    private static $allMonths = ["-", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    private static $allWeekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    public function numOfDays(){
        switch ($this->month){
            case 1: case 3: case 5: case 7: case 8: case 10: case 12: return 31;
            case 4: case 6: case 9: case 11: return 30;
            default: if(($this->year % 4 == 0 && $this->year % 100 != 0) || ($this->year % 400 == 0))return 29; else return 28;
        }
    }
    public function getAdjMonths(){
        $m=[];
        if($this->month==12){
            $m[0] = $this->year+1;
            $m[1] = 1;
        } else {
            $m[0] = $this->year;
            $m[1] = $this->month+1;
        }
        if($this->month==1){
            $m[2] = $this->year-1;
            $m[3] = 12;
        } else {
            $m[2] = $this->year;
            $m[3] = $this->month-1;
        }
        return $m;
        
    }
    public function getMonthString(){
        return self::$allMonths[$this->month].' '.$this->year;
    }


    private function firstWeekday(){
        return date("w", strtotime($this->year."-".$this->month."-1"));
    }

    private function readDays($string){
        $string = explode("\n", $string);
        array_pop($string);
        foreach($string as $s){
            $d = explode(" --- ",$s);
            if(!isset($d[1]))continue;
            $date = explode("-",$d[0]);
            $this->days[$date[2]] = new Day($date[2]);
            $this->days[$date[2]] -> readEvents($d[1]);
            ksort($this->days);
        }
    }

    public function __construct($year, $month){
        $this->year=$year;
        $this->month=$month;
        $address = "event/".$this->year."-".$this->month.".txt";
        $monthString = file_exists($address) ? file_get_contents($address) : "";
        $this->readDays($monthString);
    }


    private function cellString($d,$mode, $optional){
        $string="";
        if(isset($this->days[$d])){
            $eventString = $this->days[$d]->eventString($this->year,$this->month,$d,$mode);
            if($this->days[$d]->isBlank()){
                $cellStyle = "NoEvent";
                $isEvent = true;
            } else {

                $cellStyle = "";
                $isEvent = true;
            }
        } else {
            $cellStyle = "NoEvent";
            $isEvent = false;
        }
        if($mode!=2 && strtotime($this->year."-".$this->month."-".$d)<strtotime(date("Y")."-".date("n")."-".date("d")))$cellStyle="NoEvent";
        if($mode!=2 && strtotime($this->year."-".$this->month."-".$d)==strtotime(date("Y")."-".date("n")."-".date("d")))$cellStyle="Today";
        if($mode==2 && $optional == $d)$cellStyle="Today";
        $editLink="<a class='dates".$cellStyle."' href='eventEdit.php?Y=".$this->year."&M=".$this->month."&D=".$d."'>".$d."</a>";        
        $string.= "<td class='background".$cellStyle."'>".$editLink."<br>";
        if($isEvent){
            $string.= $eventString;
        }
        $string.= "</td>";
        return $string;
    }

    protected function displayEditInterface($day){

    }

    public function displayMonth($day, $mode, $optional=0) {
        //$monthLink =self::$allMonths[$this->month].' '.$this->year;
        $arrows=["",""];
        if($mode==2){
            $m = $this->getAdjMonths();
            $arrows[1]="<a class='yearMonth' href='eventEdit.php?Y=".$m[0]."&M=".$m[1]."&D=".$optional."'>>></a>";
            $arrows[0]="<a class='yearMonth' href='eventEdit.php?Y=".$m[2]."&M=".$m[3]."&D=".$optional."'><<</a>";
        }
        
        echo '<tr><th class="yearMonth">'.$arrows[0].'</th>
        <th colspan="5" class="yearMonth">'.$this->getMonthString().'</th>
        <th class="yearMonth">'.$arrows[1].'</th>
        </tr> ';
        if($mode>0){
            $nom=$this->numOfDays();
            echo '<tr>';
            for($head=0; $head<7; $head++){
                echo "<th width='180' class='day'>".self::$allWeekdays[$head]."</th>";
            }
            echo "</tr>";
        } else {
            $nom=min($this->numOfDays(),date("d"));    
        }
        echo "<tr height='80' valign='top'>";
        if($day>0){
            $start=max($day-date("w", strtotime($this->year."-".$this->month."-".$day)),1);
        } else {
            $start=1;
        }    
        $firstDay = date("w", strtotime($this->year."-".$this->month."-".$start));
        if ($firstDay!=0)echo "<td colspan='".$firstDay."'></td>";  
        for($d=$start; $d<=$nom; $d++){
            echo $this->cellString($d,$mode, $optional);

            if(($d+date("w", strtotime($this->year."-".$this->month."-1"))-1)%7==6){
                echo "</tr>";
                if($d<$nom){
                    echo "<tr height='90' valign='top'>";
                }
            }
        }

        if($mode==2){
            $this->displayEditInterface($optional);
        }
    }

    public function newEvent($D, $h, $m, $content, $status){
        if(!isset($this->days[$D])){
            $this->days[$D] = new Day($D);
            ksort($this->days);
        }
        $this->days[$D]->addEvent($h, $m, $content, $status);
    }
    public function changeStatus($D, $i, $newStatus = 9){
        if(!isset($this->days[$D])){
            return;
        }
        $this->days[$D]->events[$i]->status = $newStatus;
    }
    public function removeEvent($D, $i=0){
        if(!isset($this->days[$D])){
            return;
        }
        $this->days[$D]->deleteEvent($i);
        if(count($this->days[$D]->events)==0){
            unset($this->days[$D]);
        }
    }

    public function saveMonth(){
        $string="";
        foreach($this->days as $d){
            $string .= $this->year."-".$this->month."-".$d->getDay()." --- ".$d->writeEvents()."\n";
        }
        file_put_contents("event/".$this->year."-".$this->month.".txt",$string);
    }

    function proceed($get,$post){
        if(!isset($post["pw"])){
            return 0;
        }
        if($post["pw"]==""){
            return 0;
        }
        $verif = file_get_contents('verif.txt');
        if(strcmp($post["pw"],$verif)!=0){
            return -1;
        }
        if($post["mode"]=="add"){
            if($post["event"]=="") return -3;
            $checkEvent=str_replace([":",","],["",""],$post["event"]);
            if(strcmp($checkEvent,$post["event"])!=0)return -4;
            $time=explode(":",$post["time"]);
            switch($post["type"]){
                case 0: $status=0;break;
                case 1: $status=1;break;
            }
    
            $this->newEvent($get["D"], $time[0], $time[1], $post["event"], $status);
            $this->saveMonth();
            return 1;
        }
        if($post["mode"]=="cross"){
            switch($post["change"]){
                case 9: 
                    $this->changeStatus($get["D"], $post["selection"]);
                break;

                case 5: 
                    $this->changeStatus($get["D"], $post["selection"],5);
                break;

                case 2: 
                    $this->changeStatus($get["D"], $post["selection"],0);
                break;

                case 1: 
                    $this->changeStatus($get["D"], $post["selection"],1);
                break;

            }
            $this->saveMonth();
            return 1;
        }
        if($post["mode"]=="move"){
            if(!isset($this->days[$get["D"]])){
                return 0;
            }
            $event = $this->days[$get["D"]]->events[$post["selection1"]];
            $time = explode(":",$event->getTime());
            $content = $event->getContent();
            $status = $event->getStatus();
            if($post["mod"]==0)
                $this->removeEvent($get["D"], $post["selection1"]);
            $this->newEvent($post["date"], $time[0], $time[1], $content, $status);
            $this->saveMonth();
            return 1;
        }
        if($post["mode"]=="delete"){
            $this->removeEvent($get["D"], $post["selection2"]);
            $this->saveMonth();
            return 1;
        }
    }

}


?>