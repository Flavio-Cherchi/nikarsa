<?php
session_start();

class Building {

    public function __construct() {
        
    }

    public static function findCommunityID($ID_player, $ID_turn) {
        $conn = connessione::start();
        $sql = " SELECT * FROM communities WHERE ID_player = $ID_player AND ID_turn = $ID_turn ";

        if ($ris = $conn->query($sql)) {
            $row = $ris->fetch_assoc();
            $ID_community = $row["ID"];
        }

        return $ID_community;
    }

    public static function findSameBuilding($ID_community, $ID_building, $ID_turn) {
        $conn = connessione::start();
        $sql = " SELECT buildings.numMax as numbase FROM buildings "
                . "WHERE ID = $ID_building ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $numBase = $row['numbase'];

        $sql = " SELECT max(numBuilding) as numNow FROM com_build "
                . " WHERE ID_community = $ID_community AND ID_building = $ID_building  AND ID_turn = $ID_turn ";
        if ($ris = $conn->query($sql)) {
            $row = $ris->fetch_assoc();
            $numNow = $row["numNow"];
        }
        if (!$numNow) {
            $res = 1;
        } elseif ($numNow < $numBase) {
            $res = ++$numNow;
        } elseif ($numNow == $numBase) {
            $res = 0;
        }

        return $res;
    }

    public static function insert($name, $description, $level, $class, $numMax, $underCostruction, $food, $tool, $drug, $weapon, $popCostruction, $popMax) {
        $conn = connessione::start();
        echo $sql = " INSERT INTO buildings (name, description, level, class, numMax, underCostruction, food, tool, weapon, popCostruction, popMax) "
        . "VALUES ('$name', '$description', '$level', '$class', '$numMax', '$underCostruction', '$food', '$tool', '$weapon', '$popCostruction', '$popMax') ";
        $ris = $conn->query($sql);
    }

    public static function select($ID) {
        $conn = connessione::start();
        $sql = " SELECT * FROM buildings WHERE ID = $ID ";

        if ($ris = $conn->query($sql)) {
            $row = $ris->fetch_assoc();
            $name = $row["name"];
            $description = $row["description"];
            $level = $row["level"];
            $class = $row["class"];
            $numMax = $row["numMax"];
            $underCostruction = $row["underCostruction"];
            $food = $row["food"];
            $tool = $row["tool"];
            $weapon = $row["weapon"];
            $popCostruction = $row["popCostruction"];
            $popMax = $row["popMax"];
        }
    }

    public static function update($ID, $name, $description, $level, $class, $numMax, $underCostruction, $food, $tool, $weapon, $popCostruction, $popMax) {
        $conn = connessione::start();

        $sql = " UPDATE buildings SET "
                . "name = '$name', "
                . "description = '$description', "
                . "level = '$level',"
                . "class = '$class',"
                . "numMax = '$numMax',"
                . "underCostruction = '$underCostruction',"
                . "food = '$food',"
                . "tool = '$tool',"
                . "weapon = '$weapon',"
                . "popCostruction = '$popCostruction',"
                . "popMax = '$popMax' "
                . "WHERE ID = $ID ";
        $ris = $conn->query($sql);
    }

    public static function delete($ID, $name, $level) {
        $conn = connessione::start();
        $sql = " DELETE FROM buildings WHERE ID = $ID ";

        if ($conn->query($sql)) {
            ?>
            <div class="alert alert-success" role="alert">
                <div class="alert-close">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                        <span aria-hidden="true"><i class="la la-close"></i></span>
                    </button>
                </div>
                <div class="alert-text">
                    <p>Edificio <?php echo $name . " livello " . $level . " eliminato. "; ?></p>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-danger" role="alert">
                <div class="alert-close">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                        <span aria-hidden="true"><i class="la la-close"></i></span>
                    </button>
                </div>
                <div class="alert-text">
                    <p>Errore nella procedura</p>
                </div>
            </div>
            <?php
        }
    }

    /* Functions between communities and buldings */

    public static function insertAssociation($ID_community, $ID_building, $ID_turn, $underCostruction) {
        $conn = connessione::start();
        $sql = " INSERT INTO com_build (ID_community, ID_building, ID_turn, underCostruction, abandoned) "
                . "VALUES ($ID_community, $ID_building, $ID_turn, $underCostruction, 0); ";

        $ris = $conn->query($sql);
    }

    public static function selectAssociation($ID_community, $ID_turn) {
        $conn = connessione::start();
        $sql = "SELECT * FROM com_build WHERE ID_community = $ID_community AND ID_turn = $ID_turn;";
        if ($ris = $conn->query($sql)) {

            while ($row = $ris->fetch_assoc()) {
                $ID_com_build = $row['ID'];
                $ID_building = $row['ID_building'];
                $underCostruction = $row['underCostruction'];
                $abandoned = $row['abandoned'];
            }
        }
    }

    public static function updateAssociation($ID_com_build, $underCostruction, $abandoned) {
        $conn = connessione::start();
        $sql = " UPDATE com_build SET underCostruction = $underCostruction, abandoned = $abandoned "
                . "WHERE ID = $ID_player; ";
        $ris = $conn->query($sql);
    }
    
    /*Add or remove resources (food, tool, drug, weapon) when a turn starts*/
    public static function newTurn($ID_community, $ID_building, $abandoned, $ID_turn) {
        $conn = connessione::start();
        
        if ($abandoned) {
            echo "<br>abandoned -> " . $abandoned;
            switch ($ID_building) {
                case 5: //Campo arato lev 1
                    $food = 2 * $abandoned;
                    $sql = " UPDATE communities SET food = food + $food WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 9: //Campo arato lev 2
                    $food = 3 * $abandoned;
                    echo $sql = " UPDATE communities SET food = food + $food WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 21: //Infermeria lev 1
                    if ($abandoned == 1) {
                        $drug = 1;
                        echo $sql = " UPDATE communities SET drug = drug + $drug WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    } elseif ($abandoned == 2) {
                        $drug = 2;
                        $hygiene = 5;
                       echo $sql = " UPDATE communities SET drug = drug + $drug, hygiene = hygiene + $hygiene WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    }
                    break;
                    
                case 22: //Infermeria lev 2
                    if ($abandoned == 1) {
                        $drug = 2;
                        echo $sql = " UPDATE communities SET drug = drug + $drug WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    } elseif ($abandoned == 2) {
                        $drug = 4;
                        $hygiene = 5;
                       echo $sql = " UPDATE communities SET drug = drug + $drug, hygiene = hygiene + $hygiene WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    }

                case 23: //Officina lev 1
                    $tool = $abandoned;
                    $sql = " UPDATE communities SET tool = tool + $tool WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 24: //Officina lev 2
                    $tool = 2 * $abandoned;
                    $sql = " UPDATE communities SET tool = tool + $tool WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                default:
                    break;
            }
        }
    }
    
    /*Add or remove resources (food, tool, drug, weapon) in useBuilding page*/
   public static function add_remove($ID_community, $ID_building, $ID_turn, $add) {
        $conn = connessione::start();
        
            switch ($ID_building) {
                case 5: //Campo arato lev 1
                    $food = 2 * $add;
                    $sql = " UPDATE communities SET food = food + $food WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 9: //Campo arato lev 2
                    $food = 3 * $add;
                    echo $sql = " UPDATE communities SET food = food + $food WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 21: //Infermeria lev 1
                    if ($add == 1) {
                        $drug = 1 * $add;
                        echo $sql = " UPDATE communities SET drug = drug + $drug WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    } elseif ($add == 2) {
                        $drug = 2 * $add;
                        $hygiene = 5;
                       echo $sql = " UPDATE communities SET drug = drug + $drug, hygiene = hygiene + $hygiene WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    }
                    
                case 22: //Infermeria lev 2
                    if ($add == 1) {
                        $drug = 2 * $add;
                        echo $sql = " UPDATE communities SET drug = drug + $drug WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    } elseif ($add == 2) {
                        $drug = 4 * $add;
                        $hygiene = 5;
                       echo $sql = " UPDATE communities SET drug = drug + $drug, hygiene = hygiene + $hygiene WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                        $ris = $conn->query($sql);
                    }


                    break;

                case 23: //Officina lev 1
                    $tool = $add;
                    $sql = " UPDATE communities SET tool = tool + $tool WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                case 24: //Officina lev 2
                    $tool = 2 * $add;
                    $sql = " UPDATE communities SET tool = tool + $tool WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
                    $ris = $conn->query($sql);
                    break;

                default:
                    break;
            }
    }
    
    
    /*Just a description for useBuilding page*/
    public static function effects($ID_com) {
        $conn = connessione::start();

        $sql = "SELECT * FROM com_build "
                . "INNER JOIN communities ON communities.ID = com_build.ID_community "
                . "INNER JOIN buildings ON buildings.ID = com_build.ID_building "
                . "WHERE com_build.ID = $ID_com  "
                . "ORDER BY class;";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $abandoned = $row['abandoned'];
        $numBuilding = $row['numBuilding'];
        $name = $row['name'];
        $level = $row['level'];
        $underCostruction = $row['underCostruction'];


        if ($abandoned == '0') {
            return "non attivo";
        } else {
            switch ($name) {
                case "Campo arato":
                    if ($level == 1) {
                        return "+" . $abandoned * 2 . " cibo";
                    } else {
                        return "+" . $abandoned * 3 . " cibo";
                    }
                    break;

                case "Stia":
                    if ($level == 1) {
                        return "+" . $abandoned * 4 . " cibo";
                    } else {
                        return "+" . $abandoned * 8 . " cibo";
                    }
                    break;

                case "Palizzata":
                    if ($level == 1) {
                        return "Difesa attiva (0-1 Zombie)";
                    } elseif ($level == 2) {
                        return "Difesa attiva (0-5 Zombie)";
                    } else {
                        return "Difesa attiva (0-10 Zombie)";
                    }
                    break;

                case "Torre d'avvistamento":
                    if ($abandoned == 1) {
                        return "Attacchi diminuiti del 5%";
                    } else {
                        return "Attacchi diminuiti del 10%";
                    }
                    break;

                case "Infermeria":
                    if ($level == 1) {
                        if ($abandoned == 1) {
                            return "+1 medicine";
                        } else {
                            return "+2 medicine. Igiene migliorata";
                        }
                    } else {
                        if ($abandoned == 1) {
                            return "+2 medicine. Igiene migliorata";
                        } else {
                            return "+4 medicine. Igiene migliorata";
                        }
                    }
                    break;

                case "Officina":
                    if ($level == 1) {
                        return "+" . $abandoned * 1 . " utensili";
                    } else {
                        return "+" . $abandoned * 2 . " utensili";
                    }
                    break;

                case "Latrina":
                    return "Igiene aumentata";
                    break;

                case "Cisterna per l'acqua piovana":
                    return "Igiene aumentata";
                    break;

                case "Ambasciata":
                    return "Diplomazia abilitata";
                    break;

                case "Magazzino":
                    return "Commercio abilitato";
                    break;

                case "Avamposto":
                    return "+" . $abandoned * 1 . " cibo <br>+" . $abandoned * 1 . " medicine <br>+" . $abandoned * 1 . " utensili <br>";
                    break;

                case "Teste mozzate lungo le mura":
                    return "+1 punto violenza";
                    break;

                case "Centro di riciclaggio":
                    return "+1 punto autarchia";
                    break;

                case "Circolo ricreativo":
                    return "+1 punto cooperazione";
                    break;

                default:
                    break;
            }
        }
    }

}

session_commit();
