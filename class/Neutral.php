<?php
include "./class/Fight.php";

session_start();

class Neutral {

    public function __construct() {
        
    }

    public static function journeys($ID_community) {
        $conn = connessione::start();

        $sql = " SELECT * FROM communities WHERE ID = $ID_community ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $population = $row['population'];
        $weapon = $row['weapon'];
        $armedPop = ($population > $weapon) ? $weapon : $population;

        $sql = " SELECT 
(SELECT count(class) FROM characters WHERE class = 'violent' AND ID_community = 25562) as numViolent,  
(SELECT sum(level) FROM characters WHERE class = 'violent' AND ID_community = 25562) as levViolent,
(SELECT count(class) FROM characters WHERE class = 'cooperative' AND ID_community = 25562) as numCooperative,  
(SELECT sum(level) FROM characters WHERE class = 'cooperative' AND ID_community = 25562) as levCooperative,
(SELECT count(class) FROM characters WHERE class = 'autarkic' AND ID_community = 25562) as numAutarkic,  
(SELECT sum(level) FROM characters WHERE class = 'autarkic' AND ID_community = 25562) as levAutarkic   ";

        if ($ris = $conn->query($sql)) {

            while ($row = $ris->fetch_assoc()) {
                $numViolent = ($row["numViolent"]) ? $row["numViolent"] : 0;
                $levViolent = ($row["levViolent"]) ? $row["levViolent"] : 0;
                $numCooperative = ($row["numCooperative"]) ? $row["numCooperative"] : 0;
                $levCooperative = ($row["levCooperative"]) ? $row["levCooperative"] : 0;
                $numAutarkic = ($row["numAutarkic"]) ? $row["numAutarkic"] : 0;
                $levAutarkic = ($row["levAutarkic"]) ? $row["levAutarkic"] : 0;
            }
        }

        if ($numViolent) {
             $foodV = ($levViolent*10 > rand(1, 100)) ? "ok" : "ko";
            echo "<br> Cibo razziato ->" . $food = ($foodV == 'ok') ? $numViolent * $levViolent : 0;
            $weaponV = ($levViolent*10 > rand(1, 100)) ? "ok" : "ko";
            echo "<br> Armi razziato ->" . $weapon = ($weaponV == 'ok') ? $numViolent * $levViolent : 0;
            $toolV = ($levViolent*10 > rand(1, 100)) ? "ok" : "ko";
            echo "<br> Utensili razziato ->" . $tool = ($toolV == 'ok') ? $numViolent * $levViolent : 0;
            $drugV = ($levViolent*10 > rand(1, 100)) ? "ok" : "ko";
            echo "<br> Medicine razziato ->" . $drug = ($drugV == 'ok') ? $numViolent * $levViolent : 0;
            
            echo $armedPop;
            
            
        }

        if ($numCooperative) {
             $percC = ($levCooperative*10 > rand(1, 100)) ? "ok" : "ko";
            $numPc = ($percC == 'ok') ? $numCooperative * $levCooperative : 0;
            echo "<br>Punti coop" . $numPc;
        }

        if ($numAutarkic) {
             $percA = ($levAutarkic*10 > rand(1, 100)) ? "ok" : "ko";
            $numPa = ($percA == 'ok') ? $numAutarkic * $levAutarkic : 0;
            echo "<br>Punti autar" . $numPa;
        }



        switch ($numViolent) {
            case 0:
                $percentage = 0;
                break;
            case 1:
                $percentage = 33;
                break;
            case 2:
                $percentage = 66;
                break;
            case 3:
                $percentage = 100;
                break;

            default:
                break;
        }



        //return $ID_community;
    }

}

session_commit();
