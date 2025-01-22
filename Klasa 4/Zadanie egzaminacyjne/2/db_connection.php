<?php
    class db_connection{
        public $connect;

        var $host = "localhost";
        var $dbname = "szachy";
        var $username = "root";
        var $password = "";

        public function databaseConnect(){
            $con = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
            if(!$con){
                die("Connection failed: " . mysqli_connect_error());
            }
            else{
                $this->connect = $con;
            }
        }

        public function close(){
            mysqli_close($this->connect);
        }
    }
?>