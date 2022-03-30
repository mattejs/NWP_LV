<?php
    $db_name = 'radovi';
    $dir = "backup/$db_name";

    if(!is_dir($dir)){
        if(!@mkdir($dir)){
            die("<p>Directory can't be created</p>");
        }
    }

    $time = time();
    $dbc = new mysqli($servername="localhost", $username="test", $password="test", $dbname="radovi");
    if ($dbc->connect_error) {
        die("Connection failed: " . $dbc->connect_error);
    }

    $r = $dbc->query("SHOW TABLES");
       
    if($r->num_rows > 0){
        echo "<p>Backup for database '$db_name'.</p>";
        while(list($table) = mysqli_fetch_array($r, MYSQLI_NUM)){
            $q = "SELECT * FROM $table";
            $r2 = $dbc->query($q);
            $columns = $r2->fetch_fields();
            if($r2->num_rows > 0){
                if($fp = fopen("$dir/{$table}_{$time}.txt", 'w9')){
                    while($row = mysqli_fetch_array($r2, MYSQLI_NUM)){
                        fwrite($fp, "INSERT INTO $db_name (");
                        foreach($columns as $column){
                            fwrite($fp, "$column->name");
                            if($column != end($columns)){
                                fwrite($fp, ", ");
                            }
                        }
                        fwrite($fp, ")\r\nVALUES (");
                        foreach($row as $value){
                            $value = addslashes($value);
                            fwrite($fp, "'$value'");
                            if($value != end($row)){
                                fwrite($fp, ", ");
                            }
                            else {
                                fwrite($fp, ")\";");
                            }
                        }
                        fwrite($fp, "\r\n");
                    }
                    fclose($fp);
                    echo "<p>Table $table saved.</p>";
                    if($fp2 = gzopen("$dir/{$table}_{$time}.sql.gz", 'w9')){
                        gzwrite($fp2, file_get_contents("$dir/{$table}_{$time}.txt"));
                        gzclose($fp2);
                    }
                    else{
                        echo "<p>File $dir/{$table}_{$time}.txt can't be open</p>";
                        break;
                    }
                }   
                else{
                    echo "<p>File $dir/{$table}_{$time}.txt can't be open</p>";
                    break;
                }            
            }
        }
    }
    else{
        echo "<p>DB $db_name doesn't have any tables.</p>";
    }
?>