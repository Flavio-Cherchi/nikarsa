<?php

class Fight {

    public function __construct() {
        
    }

    public static function fighterHuman($community) {
        $conn = connessione::start();

        $sql = " SELECT * FROM communities WHERE ID = $community ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $population = $row['population'];
        $weapon = $row['weapon'];
        $armedPop = ($population >= $weapon) ? $weapon : $population;
        $pViolence = $row['pViolence'];
        $foodTotal = $row['food'];
        $hygiene = $row['hygiene'];
        $communityName = $row['communityName'];

        if ($foodTotal / 2 - $armedPop >= 0) {
            $food = 3;
        } elseif ($foodTotal - $armedPop >= 0) {
            $food = 2;
        } else {
            $food = 1;
        }

        if ($hygiene >= 75) {
            $clean = 4;
          //  $cleanTot = ($baseStrength/100) * 0.25;
        } elseif ($hygiene < 75 && $hygiene >= 50) {
            $clean = 3;
          //  $cleanTot = 0;
        } elseif ($hygiene < 50 && $hygiene >= 25) {
            $clean = 2;
           // $cleanTot = (($baseStrength/100) * 0.10) * -1;
        } elseif ($hygiene < 25) {
            $clean = 1;
            //$cleanTot = (($baseStrength/100) * 0.25) * -1;
        }
        
        $sql = " SELECT 
                (SELECT count(class) FROM characters WHERE class = 'violent' AND ID_community = $community) as numViolent,  
                (SELECT sum(level) FROM characters WHERE class = 'violent' AND ID_community = $community) as levViolent";

        if ($ris = $conn->query($sql)) {

            while ($row = $ris->fetch_assoc()) {
                $numViolent = ($row["numViolent"]) ? $row["numViolent"] : 0;
                $levViolent = ($row["levViolent"]) ? $row["levViolent"] : 0;
            }
        }

        //LevViolent: every level adds 0.10 to the multiplier
        $baseStrength = (($population + $numViolent + $levViolent * 0.1) * 100);

        //$violence = floor(($pViolence / 33) * $baseStrength);   //Forse non si useranno qui.

        $hungry = 0;
        if ($food == 2) {
            $hungry = ($baseStrength * 0.25) * -1;
        }

        if ($food == 1) {
            $hungry = ($baseStrength * 0.50) * -1;
        }

        $force = $baseStrength + $cleanTot + $violence + $hungry;
        $res = array();
        /*
         * return: [0 - force], 
         * [1 - number of violent Character], 
         * [2 - sum of violent levels], 
         * [3 - armed population], 
         * [4 - max violence point can use],
         * [5 - food level], 
         * [6 - hygiene level], 
         * [7 - community name], 
         */
        array_push($res, $force, $numViolent, $levViolent, $armedPop, $pViolence, $food, $clean, $communityName);
        return $res;
    }
    
       public static function fighterHumanMod($community, $numChar, $level, $population, $pViolence, $food, $clean, $communityName) {
        $conn = connessione::start();

        //LevViolent: every level adds 0.10 to the multiplier
        $baseStrength = (($population + $numViolent + $levViolent * 0.1) * 100);

        $violence = $pViolence*100;
        $force = $baseStrength + $clean + $violence + $food;
        $res = array();
        /*
         * return: [0 - force], 
         * [1 - community name], 
         * [2 - armed population], 
         */
        array_push($res, $force, $communityName, $population);
        return $res;
    }

    public static function fighterZombie($population) {
        $conn = connessione::start();
        return floor($population * 25);
    }

    public static function realFight($community1, $community2, $zombie) {
        $conn = connessione::start();

        $force1 = Fight::fighterHuman($community1);
        $first = $force1[0];
        $population1 = $force1[3];
        
        
        if ($zombie == '0') {
            $force2 = Fight::fighterHuman($community2);
            $second = $force2[0];
            $population2 = $force2[3];
        } else {
            $second = Fight::fighterZombie($community2);
            $population2 = floor($second/25);            
        }
            $fortune1 = floor(($first / 100) * rand(1, 5));
            $fortune2 = floor(($second / 100) * rand(1, 5));
            $first += $fortune1;
            $second += $fortune2;
            
        if (($first - $second) > 0) {
                $title = "Vittoria della comunità " . $force1[7] ."!";
        } else {
            if ($zombie == '0') {
                $title = "Vittoria della comunità " . $force2[7] ."!";
            } else {
                $title = "Sconfitta della comunità " . $force1[7] ."...";
            }
        }

        if ($first > ($second * 5)) {
        //echo "quintuplo!";
        $dec1a = 0;
        $dec1b = floor($population1 / 100 * 5);
        $dec2a = floor($population2 / 100 * 95);
        $dec2b = $population2;
    } elseif ($first > ($second * 2)) {
        //echo "doppio!";
        $dec1a = floor($population1 / 100 * 10);
        $dec1b = floor($population1 / 100 * 20);
        $dec2a = floor($population2 / 100 * 80);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($first > $second) {
        //echo "più grande!";
        $dec1a = floor($population1 / 100 * 20);
        $dec1b = floor($population1 / 100 * 50);
        $dec2a = floor($population2 / 100 * 50);
        $dec2b = floor($population2 / 100 * 80);
    } elseif ($first == $second) {
        //echo "uguale!";
        $dec1a = floor($population1 / 100 * 70);
        $dec1b = floor($population1 / 100 * 90);
        $dec2a = floor($population2 / 100 * 70);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($second > $first) {
        //echo "più grande!";
        $dec2a = floor($population1 / 100 * 20);
        $dec2b = floor($population1 / 100 * 50);
        $dec1a = floor($population2 / 100 * 50);
        $dec1b = floor($population2 / 100 * 80);
    } elseif ($second > ($first * 2)) {
        //echo "doppio!";
        $dec2a = floor($population1 / 100 * 10);
        $dec2b = floor($population1 / 100 * 20);
        $dec1a = floor($population2 / 100 * 80);
        $dec1b = floor($population2 / 100 * 90);
    } elseif ($second > ($first * 5)) {
        //echo "quintuplo!";
        $dec2a = 0;
        $dec2b = floor($population1 / 100 * 5);
        $dec1a = floor($population2 / 100 * 95);
        $dec1b = $population2;
    }

    $died1 = rand($dec1a, $dec1b);
    $died2 = rand($dec2a, $dec2b);
    
        $res = array();
        /*
         * return: [0 - esito], 
         * [1 - score first community], 
         * * [2 - score second community/zombie], 
         * * [3 - name first community], 
         * * [4 - name second community/zombie], 
         * [5 - losses for first community], 
         * [6 - losses for second community/zombie]
         */
        array_push($res, $title, $first, $second, $force1[7], $force2[7], $died1, $died2);
        return $res;
    }
    
        public static function realFightMod($force1, $force2, $zombie) {
        $conn = connessione::start();
        $first = $force1[0];
        $population1 = $force1[2];
        $second = ($zombie == '0') ? $force2[0] : $force2;
        $population2 = ($zombie == '0') ? $force2[2] : $force2/25;
        
            $fortune1 = floor(($first / 100) * rand(1, 5));
            $fortune2 = floor(($second / 100) * rand(1, 5));
            $first += $fortune1;
            $second += $fortune2;
    
                    if ($first > ($second * 5)) {
        //echo "quintuplo!";
        $dec1a = 0;
        $dec1b = floor($population1 / 100 * 5);
        $dec2a = floor($population2 / 100 * 95);
        $dec2b = $population2;
    } elseif ($first > ($second * 2)) {
        //echo "doppio!";
        $dec1a = floor($population1 / 100 * 10);
        $dec1b = floor($population1 / 100 * 20);
        $dec2a = floor($population2 / 100 * 80);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($first > $second) {
        //echo "più grande!";
        $dec1a = floor($population1 / 100 * 20);
        $dec1b = floor($population1 / 100 * 50);
        $dec2a = floor($population2 / 100 * 50);
        $dec2b = floor($population2 / 100 * 80);
    } elseif ($first == $second) {
        //echo "uguale!";
        $dec1a = floor($population1 / 100 * 70);
        $dec1b = floor($population1 / 100 * 90);
        $dec2a = floor($population2 / 100 * 70);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($second > $first) {
        //echo "più grande!";
        $dec2a = floor($population1 / 100 * 20);
        $dec2b = floor($population1 / 100 * 50);
        $dec1a = floor($population2 / 100 * 50);
        $dec1b = floor($population2 / 100 * 80);
    } elseif ($second > ($first * 2)) {
        //echo "doppio!";
        $dec2a = floor($population1 / 100 * 10);
        $dec2b = floor($population1 / 100 * 20);
        $dec1a = floor($population2 / 100 * 80);
        $dec1b = floor($population2 / 100 * 90);
    } elseif ($second > ($first * 5)) {
        //echo "quintuplo!";
        $dec2a = 0;
        $dec2b = floor($population1 / 100 * 5);
        $dec1a = floor($population2 / 100 * 95);
        $dec1b = $population2;
    }

    $died2 = rand($dec1a, $dec1b);
    $died1 = rand($dec2a, $dec2b);
    
        if (($first - $second) > 0) {
                $title = "Vittoria della comunità " . $force1[1] ."!";
        } else {
            if ($zombie == '0') {
                $title = "Vittoria della comunità " . $force2[1] ."!";
            } else {
                $title = "Sconfitta della comunità " . $force1[1] ."...";
            }
        }

        $res = array();
        /*
         * return: [0 - esito], 
         * [1 - score first community], 
         * [2 - score second community/zombie], 
         * [3 - name first community], 
         * [4 - name second community/zombie], 
         * [5 - losses for first community], 
         * [6 - losses for second community/zombie], 
         */
        array_push($res, $title, $first, $second, $force1[1], $force2[1], $died1, $died2);
        return $res;
    }

    public static function simulation($numChar, $level, $population, $pViolence, $food, $clean, $zombie) {
        $conn = connessione::start();
        if (!$zombie) {

            if (!$numChar) {
                $level = 0;
                $multiplier = 1;
            } else {
                if ($level == 0) {
                    $level = 1;
                }
                $multiplier = $level * 0.1;
            }

            $baseStrength = (($population + ($numChar * 3) ) * 100) * $multiplier;

            $violence = floor(($pViolence / 33) * $baseStrength);

            $hungry = 0;
            if ($food == 2) {
                $hungry = ($baseStrength * 0.25) * -1;
            }

            if ($food == 1) {
                $hungry = ($baseStrength * 0.50) * -1;
            }

            $cleanTot = 0;
            if ($clean == 4) {
                $cleanTot = $baseStrength * 0.25;
            } elseif ($clean == 2) {
                $cleanTot = ($baseStrength * 0.10) * -1;
            } elseif ($clean == 1) {
                $cleanTot = ($baseStrength * 0.25) * -1;
            }

            return $force = $baseStrength + $cleanTot + $violence + $hungry;
        } else {
            return $force = floor($population * 25);
        }
    }

}
