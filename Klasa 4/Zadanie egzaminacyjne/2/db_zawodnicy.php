<?php
    include_once("db_connection.php");
    class db_zawodnicy extends db_connection{
        function selectZawodnicy(){
            $query = "SELECT * FROM `zawodnicy` WHERE 1";
            $data = mysqli_query($this->connect, $query);
            if (mysqli_num_rows($data) > 0){
            return $data;
            }
        }

        function insertZawodnik ($pseudonim, $tytul, $data_zdobycia, $ranking, $klasa){
            $query = "INSERT INTO `zawodnicy`(`pseudonim`, `tytul`, `data_zdobycia`, `ranking`, `klasa`) VALUES  ('".$pseudonim."','".$tytul."','".$data_zdobycia."','".$ranking."', '".$klasa."');";
            $data = mysqli_query($this->connect, $query);
            $this->close();
        }

        function deleteZawodnik ($id_zawodnika){
            $query = "Delete from posts where id_zawodnika =".$id_zawodnika.";";
            $data = mysqli_query($this->connect, $query);
            unset($_GET['id_zawodnika']);
            $this->close();
        }

        function updateZawodnik ($id_zawodnika, $pseudonim, $tytul, $data_zdobycia, $ranking, $klasa){
            $query = "UPDATE `posts` SET `id_zawodnika`='".$id_zawodnika."',`pseudonim`='".$pseudonim."',`tytul`='".$tytul."',`data_zdobycia`='".$data_zdobycia."',`ranking`='".$ranking."',`klasa`='".$klasa."' WHERE `id_zawodnika`=".$id_zawodnika.";";
            $data = mysqli_query($this->connect, $query);
            unset($_GET['id_zawodnika']);
            $this->close();
        }
    }
?>

