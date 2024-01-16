<!-- File contains all database related functions -->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_WARNING);  // disable warnings for custom error messages

// Global configuration for database
$config["dbuser"] = "ora_red3r1ct";
$config["dbpassword"] = "a29315538";
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$c = NULL;  // global database connection

$success = true;	// keep track of errors so page redirects only if there are no errors

function connectDatabase()
{
    global $c;
    global $config;

    $c = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

    if ($c) {
        return $c;
    } else {
        $e = OCI_Error();
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectDatabase()
{
    global $c;

    oci_close($c);
}

/* Executes an SQL query with optional bound variables */
function executeQuery($sql, $bindVariables = [])
{
    global $c, $success;

    $statement = oci_parse($c, $sql);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $sql . "<br>";
        $e = OCI_Error($c);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($bindVariables as $key => &$value) {
        oci_bind_by_name($statement, $key, $value);
        unset($val);
    }

    $r = oci_execute($statement, OCI_DEFAULT);

    if (!$r) {
        $e = oci_error($statement);

        // Not `parent key not found` error
        if ($e['code'] != 2291 && $e['code'] != 1722 && $e['code'] != 1) {
            echo "<br>Cannot execute the following command: " . $sql . "<br>";
            echo htmlentities($e['message']);
            echo $e['code'];
        }
        $success = False;
    }

    oci_commit($c);

    return $statement;
}

/* Add new rental to Rental table */
function handleInsert()
{
    global $success;

    $name = $_POST['guestName'];
    $phone_number = $_POST['guestPhoneNumber'];
    $serial_num = $_POST['serialNum'];
    $rental_date = $_POST['rentalDate'];
    $SIN = $_POST['sin'];

    $sql = "INSERT INTO Rental
            VALUES (:guest_name, :phone_number, :serial_num, TO_DATE(:rental_date, 'YYYY-MM-DD'), :SIN)";

    $bindVariables = [
        ':guest_name' => $name,
        ':phone_number' => $phone_number,
        ':serial_num' => $serial_num,
        ':rental_date' => $rental_date,
        ':SIN' => $SIN,
    ];

    $statement = executeQuery($sql, $bindVariables);

    if (!$success) {
        $e = oci_error($statement);
        if ($e['code'] == 2291) {
            echo "Error: At least one of Guest, Serial Number, or Employee entered does not exist.
                Please try again.";
        } else if ($e['code'] == 1) {
            echo "Error: Guest has already rented this equipment on the given day.";
        }
        $success = true;
    } else {
        echo "Insert complete";
    }
}

function handleDelete()
{
    $name = $_POST['guestName'];
    $phone_number = $_POST['guestPhoneNumber'];

    $sql = "DELETE
            FROM Guest
            WHERE guest_name = :guest_name AND phone_number = :phone_number";

    $bindVariables = [
        ':guest_name' => $name,
        ':phone_number' => $phone_number,
    ];

    $statement = executeQuery($sql, $bindVariables);
    $affectedRows = oci_num_rows($statement);
    if ($affectedRows > 0) {
        echo "Guest deleted successfully.";
    } else {
        echo "Error: Guest does not exist. Nothing deleted. Please try again.";
    }
    oci_free_statement($statement);
}

function handleUpdate()
{
    global $success;
    $name = $_POST['updateName'];
    $phone_number = $_POST['updatePhoneNumber'];
    $email = $_POST['updateEmail'];
    $gender = $_POST['updateGender'];

    if(trim($email) !='' && trim($gender) != '') {
        $sql = "UPDATE Guest
                SET email = :email, gender = :gender
                WHERE guest_name = :guest_name AND phone_number = :phone_number";

        $bindVariables = [
            ':guest_name' => $name,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':gender' => $gender,
        ];

        $statement = executeQuery($sql, $bindVariables);

        if (!$success) {
            $e = oci_error($statement);
            if ($e['code'] == 1) {
                echo "Error: Email already exists for another guest.";
            }
            $success = true;
            oci_free_statement($statement);
            return;
        }
        
        $affectedRows = oci_num_rows($statement);

        if ($affectedRows == 0) {
            echo "Error: No such guest exists. Please try again.";
        } else {
            echo "Guest email and gender successfully.";
        }
    } else if(trim($email) !='' && trim($gender) == '') {
        $sql = "UPDATE Guest
                SET email = :email
                WHERE guest_name = :guest_name AND phone_number = :phone_number";

        $bindVariables = [
            ':guest_name' => $name,
            ':phone_number' => $phone_number,
            ':email' => $email,
        ];

        $statement = executeQuery($sql, $bindVariables);

        if (!$success) {
            $e = oci_error($statement);
            if ($e['code'] == 1) {
                echo "Error: Email already exists for another guest.";
            }
            $success = true;
            oci_free_statement($statement);
            return;
        }

        $affectedRows = oci_num_rows($statement);
        
        if ($affectedRows == 0) {
            echo "Error: No such guest exists. Please try again.";
        } else {
            echo "Guest email updated successfully.";
        }
    } else if(trim($email) =='' && trim($gender) != '') {
        $sql = "UPDATE Guest
                SET gender = :gender
                WHERE guest_name = :guest_name AND phone_number = :phone_number";

        $bindVariables = [
            ':guest_name' => $name,
            ':phone_number' => $phone_number,
            ':gender' => $gender,
        ];

        $statement = executeQuery($sql, $bindVariables);
        $affectedRows = oci_num_rows($statement);

        if ($affectedRows == 0) {
            echo "Error: No such guest exists. Please try again.";
        } else {
            echo "Guest gender successfully.";
        }
    } else {
        echo "No updatable information provided.";
        return;
    }

    oci_free_statement($statement);

}

function handleSelectionCreate()
{
    $n=(int)$_GET['clauses']-1;
    echo'<p>Select filters for equipment:</p>';

   echo'<form method="GET" action="database.php">
   <input type="hidden" id="selectRequest" name="selectRequest">';
   while($n>0){
    $n-=1;
            echo'Equipment <select name="column[]" >
            <option value="equipment_type">TYPE</option>
            <option value="equipment_size">SIZE</option>
            <option value="cost">COST</option>
            <option value="brand">BRAND</option>
        </select>
        = <input type="text" name="values[]">
        <br>
        <select name="op[]" style="margin: 5px 5px 0px 0px;">
            <option value="AND"> AND</option>
            <option value="OR"> OR</option>
        </select> 
        <br /><br />';
   }
   echo'Equipment <select name="column[]" >
			<option value="equipment_type">TYPE</option>
			<option value="equipment_size">SIZE</option>
			<option value="cost">COST</option>
			<option value="brand">BRAND</option>
		</select>
		 = <input type="text" name="values[]" required>';

    echo'<p><input type="submit" value="filter" name="selectionSubmit"></p>
    </form>';
}

function handleSelection(){
    global $success;

    $column=$_GET['column'];
    $values=$_GET['values'];
   
    $n=count($column);
    $where='';
    if($n==1){
        $where= $column[0]."='".$values[0]."'";
    }else{
        $ops=$_GET['op'];
        for ($x = 0; $x < $n-1; $x++) {
            $where.= $column[$x]."='".$values[$x]."' ". $ops[$x].' ';
          }
          $where.=$column[$n-1]."='".$values[$n-1]."'";
    }
    $sql="SELECT * 
    FROM EQUIPMENT
    WHERE ". $where;
    $statement = executeQuery($sql);

    if (!$success) {
        $e = oci_error($statement);
        if ($e['code'] == 1722) {
            echo "Error: Cost should be a number not string. Please try again.";
        }
        $success = true;
        oci_free_statement($statement);
        return;
    }

    $print_table = "<table border='5'><tr>
            <th>Serial Number</th>
            <th>Equipment Type</th>
            <th>Equipment Size</th>
            <th>Equipment Cost</th>
            <th>Equipment Brand</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
    do{
        if(!$row){
            echo "No rows for the given selection filters.";
            oci_free_statement($statement);
            return;
        }
        $print_table .= "<tr>
        <td>" . $row['SERIAL_NUM'] . "</td>
        <td>" . $row['EQUIPMENT_TYPE'] . "</td>
        <td>" . $row['EQUIPMENT_SIZE'] . "</td>
        <td>" . $row['COST'] . "</td>
        <td>" . $row['BRAND'] . "</td>
        </tr>";

    } while($row = oci_fetch_assoc($statement));
    echo $print_table;
    echo "</table>";

    oci_free_statement($statement);
}

/* Get guest's rental equipment information */
function handleJoin()
{
    $guest_name = $_GET['guestName'];
    $phone_number = $_GET['phoneNumber'];

    $sql = "SELECT Equipment.*, Rental.rental_date, Rental.guest_name, Rental.phone_number
            FROM Equipment
            JOIN Rental ON Equipment.serial_num = Rental.serial_num
            WHERE Rental.guest_name = :guest_name
                AND Rental.phone_number = :phone_number";

    $bindVariables = [
        ':guest_name' => $guest_name,
        ':phone_number' => $phone_number,
    ];

    $statement = executeQuery($sql, $bindVariables);
    
    $print_table = "<table border='5'><tr>
            <th>Serial Number</th>
            <th>Rental Date</th>
            <th>Guest Name</th>
            <th>Guest Phone #</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
    do{
        if(!$row){
            echo "No purchases made by given guest.";
            oci_free_statement($statement);
            return;
        }
        $print_table .= "<tr>
        <td>" . $row['SERIAL_NUM'] . "</td>
        <td>" . $row['RENTAL_DATE'] . "</td>
        <td>" . $row['GUEST_NAME'] . "</td>
        <td>" . $row['PHONE_NUMBER'] . "</td>
        </tr>";

    } while($row = oci_fetch_assoc($statement));
    echo $print_table;
    echo "</table>";

    oci_free_statement($statement);
}

function handleGroupBy()
{
    $guest_name = $_GET['guestName'];
    $phone_number = $_GET['guestPhoneNumber'];

    $sql = "SELECT  e.equipment_type, AVG(e.cost) AS Per_Rental
            FROM Rental r, Equipment e
            WHERE r.serial_num = e.serial_num
                AND r.guest_name = :guest_name
                AND r.phone_number = :phone_number
            GROUP BY e.equipment_type";

    $bindVariables = [
        ':guest_name' => $guest_name,
        ':phone_number' => $phone_number,
    ];

    $statement = executeQuery($sql, $bindVariables);

    $print_table = "<table border='5'><tr>
            <th>Equipment Type</th>
            <th>Average Rental Cost</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
    do{
        if(!$row){
            echo "No purchases made by given guest.";
            oci_free_statement($statement);
            return;
        }
        $print_table .= "<tr>
        <td>" . $row['EQUIPMENT_TYPE'] . "</td>
        <td>" . $row['PER_RENTAL'] . "</td>
        </tr>";

    } while($row = oci_fetch_assoc($statement));
    echo $print_table;
    echo "</table>";

    oci_free_statement($statement);
}

function handleHaving()
{
    $sql = "SELECT guest_name,phone_number, COUNT(*) AS times_serviced
            FROM Rental
             WHERE SIN = ".$_GET['SIN']."
             GROUP BY guest_name,phone_number
             HAVING COUNT(*) > 1";

    $statement = executeQuery($sql);

    $print_table = "<table border='5'><tr>
            <th>Guest Name</th>
            <th>Guest Phone #</th>
            <th>Times Serviced</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
    do{
        if(!$row){
            echo "No repeated guests of given employee.";
            oci_free_statement($statement);
            return;
        }
        $print_table .= "<tr>
        <td>" . $row['GUEST_NAME'] . "</td>
        <td>" . $row['PHONE_NUMBER'] . "</td>
        <td>" . $row['TIMES_SERVICED'] . "</td>
        </tr>";

    } while($row = oci_fetch_assoc($statement));
    echo $print_table;
    echo "</table>";
    oci_free_statement($statement);
}

/* Get average total cost of rental equipment per guest */
function handleNestedAgg()
{
    $sql = "SELECT COUNT(distinct CONCAT(r.guest_NAME,r.phone_number)) AS number_of_guests 
    FROM Equipment e1,Rental r 
    WHERE r.serial_num=e1.serial_num AND e1.cost = ANY(SELECT MAX(e2.cost) FROM Equipment e2 GROUP BY e2.equipment_type)";

    $statement = executeQuery($sql);
    echo "<table border='5'>";
    echo "<tr>
            <th>Number of guests</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
        echo "<tr>
        <td>" . $row['NUMBER_OF_GUESTS'] . "</td>
        </tr>";
    
    echo "</table>";

    oci_free_statement($statement);
}

/*DIVISION */
function handleDivision()
{

    $sql = "SELECT DISTINCT g.guest_name,g.phone_number
	FROM Guest g
		WHERE NOT EXISTS (SELECT e.SIN
						FROM Employee e
						 WHERE NOT EXISTS (SELECT r.guest_name,r.phone_number,r.SIN
											FROM Rental r
											WHERE e.SIN=r.SIN
											AND g.guest_name=r.guest_name AND g.phone_number=r.phone_number))";

    
    $statement = executeQuery($sql);

    $print_table = "<table border='5'><tr>
            <th>Guest Name</th>
            <th>Guest Phone #</th>
            </tr>";
    $row = oci_fetch_assoc($statement);
    do{
        if(!$row){
            echo "No guests assigned to all employees at least once.";
            oci_free_statement($statement);
            return;
        }
        $print_table .= "<tr>
        <td>" . $row['GUEST_NAME'] . "</td>
        <td>" . $row['PHONE_NUMBER'] . "</td>
        </tr>";

    } while($row = oci_fetch_assoc($statement));
    echo $print_table;
    echo "</table>";

    oci_free_statement($statement);
}

function handleQuery() {

    global $table_name;

    $values="";
    $n=0;
    
    foreach($_GET['columns'] as $column){
        if($n>0){
            $values.=" ,";
        }
        $values.=$column;
        $n+=1;
    }
    
    $sql= "SELECT ". $values ." FROM ". $_GET['table_list'];
    $statement = executeQuery($sql);
    echo "<table border='5'>";
    echo "<tr>";
    foreach($_GET['columns'] as $column){
        echo "<th>" . $column . "</th>";
    }
    echo "</tr>";

    while ($row = oci_fetch_assoc($statement)) {
        echo "<tr>";
        foreach($_GET['columns'] as $column){
            echo "<td>" . $row[$column] . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    oci_free_statement($statement); 
}

 function handleCheck(){

    global $table_name;

    $table_name = $_GET['table_list'];

    $sql = "select column_name from user_tab_cols where table_name = '". $table_name."'";

    $statement = executeQuery($sql);

    echo ' <form  method="get"action="database.php">';
    echo '<input type="hidden" id="table_list" name="table_list" value="'.$table_name.'" />';

    while ($row = oci_fetch_assoc($statement)) {
        echo'<input type="checkbox" name="columns[]" value="'.$row['COLUMN_NAME'].'">'.$row['COLUMN_NAME'].'</input>';
    } 

    echo' <input type="submit" name="projectValues" value="Submit"/>
        </form>';   
    
    oci_free_statement($statement);
} 

function handleProjection()
{
    $sql = "select table_name from tabs";

    $statement = executeQuery($sql);   
    
    echo '<form method="get" action="database.php">
        <label for="color">Choose a table:</label>
        <select name="table_list" id="table_list">';

    while ($row = oci_fetch_assoc($statement)) {
        echo '<option value="'.$row['TABLE_NAME'].'">'.$row['TABLE_NAME'].'</option>';
    }

    echo '</select>
        <input type="submit" value="Select" name="projectionSub"></p>
        </form>';

    oci_free_statement($statement);
}


function handlePOSTRequest()
{
    if (connectDatabase()) {
        if (array_key_exists('insertRequest', $_POST)) {
            handleInsert();
        } else if (array_key_exists('deleteRequest', $_POST)) {
            handleDelete();
        } else if (array_key_exists('updateRequest', $_POST)) {
            handleUpdate();
        }

        disconnectDatabase();
    }
}

function handleGETRequest()
{
    if (connectDatabase()) {
        if (array_key_exists('joinRequest', $_GET)) {
            handleJoin();
        } else if (array_key_exists('groupByRequest', $_GET)) {
            handleGroupBy();
        } else if (array_key_exists('havingRequest', $_GET)) {
            handleHaving();
        } else if (array_key_exists('nestedAggRequest', $_GET)) {
            handleNestedAgg();
        } else if (array_key_exists('divisionRequest', $_GET)) {
            handleDivision();
        } else if (array_key_exists('projectValues', $_GET)) {
            handleQuery();
        } else if (array_key_exists('projectionSub', $_GET)) {
            handleCheck();
        } else if (array_key_exists('projectionCreate', $_GET)) {
            handleProjection();
        }if (array_key_exists('selectN', $_GET)) {
            handleSelectionCreate();
        }if (array_key_exists('selectRequest', $_GET)) {
            handleSelection();
        }


        disconnectDatabase();
    }
}

// Handle POST and GET requests made from the front-end
if (isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['joinSubmit']) || isset($_GET['groupBySubmit']) || isset($_GET['havingSubmit']) || 
            isset($_GET['nestedAggSubmit']) || isset($_GET['divisionSubmit']) || isset($_GET['projectValues']) ||
            isset($_GET['projectionSub']) || isset($_GET['projectionCreate']) || isset($_GET['nSubmit'])
            || isset($_GET['selectionSubmit'])) {

    handleGETRequest();
}
?>
