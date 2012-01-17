<?php

// Connect To Database
$db_host='localhost';
$db_user='root';
$db_password='root';
$db_db='database';

$link = mysql_connect($db_host, $db_user, $db_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Select Database
mysql_select_db($db_db);

// Get Tables List
$sql = "SHOW TABLES FROM $db_db";
$result = mysql_query($sql);

while ($row = mysql_fetch_row($result)) {

    $t=$row[0];

    // Create Model.php
    echo "Creating Model File: ".$t.".php \n";
    $file = strtoupper($t).".php";
    $fh = fopen($file,'w');
    
    // Write Basic Data
    fwrite($fh,"<?php\n");
    fwrite($fh,"\n");
    fwrite($fh,"class Model_".strtoupper($t)." extends Model_Table {\n");
    fwrite($fh,"   public \$entity_code='".$t."';\n");
    fwrite($fh,"\n");
    fwrite($fh,"   function init(){ \n");
    fwrite($fh,"      parent::init();\n");
    fwrite($fh,"\n");
    ////  
    
    echo "Creating Model For Table: {$t}\n";

    $res1 = mysql_query("SHOW COLUMNS FROM $t");
    if (!$res1) {
       echo 'Could not run query: ' . mysql_error();
       exit;
    }

    /*
    Array
    (
        [Field] => FECHA_AUT_PROVISORIA
        [Type] => datetime
        [Null] => YES
        [Key] => 
        [Default] => 
        [Extra] => 
    )
    */

    if (mysql_num_rows($res1) > 0) {
        while ($row = mysql_fetch_assoc($res1)) {
            //print_r($row);
	    $f=$row['Field'];
	    $n=$row['Null'];
	    $k=$row['Key'];
	    echo "Writing: ".$row['Field'];
     	    if ( ( $n == "NO" ) || ( $k == "PRI" ) )
 	    {
	       echo " Mandatory....\n";
               fwrite($fh,"      \$this->addField('".$f."')->mandatory('true');\n");
	    } else {
	       echo " Not Mandatory...\n";
	       fwrite($fh,"      \$this->addField('".$f."');\n");	
	    } 
        }
    }

    fwrite($fh,"   }\n");
    fwrite($fh,"}\n");
    fwrite($fh,"?>\n");
    fclose($fh);

}

mysql_free_result($result);
mysql_close($link);
?>

