<!-- Main page that stores all html and calls functions from database.php -->

<html>

<h1>MOUNTAIN RESORT MANAGEMENT SYSTEM</h1>
<style type="text/css">
  @import "compass/css3";

html {
  font: 81.25% arial, helvetica, sans-serif;
  color: #333;
  line-height: 1;
}


/* FORM */
form {
  margin: 10px 290px;
  width: 362px;
  padding: 25px;
  background: #f0f0f0;
  border: 1px solid #e5e5e5;
  div {
    position: relative;
    margin: 0 0 1.5em;
  }
}

/* regular input */
 input[type=number],input[type=text] {
  display: inline-block;
  height: 29px;
  margin-top: 4px;
  margin-bottom: 3px;
  padding: 0 8px;
  background: #fff;
  border: 1px solid #d9d9d9;
  border-top: 1px solid #c0c0c0;
  box-sizing: border-box;
  border-radius: 1px;
  width: 100%;
  
}
input[type=submit] {
    padding:5px 15px; 
    background:#ccc; 
    border:0 none;
    cursor:pointer;
}
</style>

	<!-- insert -->
	<div>
	<form method="POST" action="database.php">
			<input type="hidden" id="insertRequest" name="insertRequest" required>
			Guest Name: <input type="text" name="guestName" maxlength="50" required> <br /><br />
			Phone Number: <input type="text" name="guestPhoneNumber" maxlength="20" required> <br /><br />
			Serial number: <input type="text" name="serialNum" maxlength="25" required> <br /><br />
			Rental date: <input type="date" name="rentalDate"  min="2010-01-01" max="2023-12-07" format="yyyy-mm-dd" required> <br /><br />
			SIN: <input type="number" name="sin" required> <br /><br />

			<input type="submit" value="Insert Rental" name="insertSubmit"></p>
		</form>
	</div>

	<!-- delete -->
	<form method="POST" action="database.php">
		<input type="hidden" id="deleteRequest" name="deleteRequest">
		Guest Name: <input type="text" name="guestName" maxlength="50" required> <br /><br />
		Phone Number: <input type="text" name="guestPhoneNumber" maxlength="20" required> <br /><br />
		<input type="submit" value="Delete Guest" name="deleteSubmit"></p>
	</form>

	<!-- update -->
	<form method="POST" action="database.php">
		<input type="hidden" id="updateRequest" name="updateRequest">
		Guest Name: <input type="text" name="updateName" maxlength="50" required> <br /><br />
		Phone Number: <input type="text" name="updatePhoneNumber" maxlength="20" required> <br /><br />
		Email: <input type="text" name="updateEmail" maxlength="50" > <br /><br />
		Gender: <input type="text" name="updateGender" maxlength="20" > <br /><br />

		<input type="submit" value="Update Guest" name="updateSubmit"></p>
	</form>

	<!-- projection button-->
	<form method="GET" action="database.php">
		<p>Choose table to view:</p>
		<p><input type="submit" value="Projection" name="projectionCreate"></p>
	</form>

	<!-- having button-->
	<form method="GET" action="database.php">
		<p style="padding:2px;">Repeat customers of employee :</p>
		<input type="hidden" id="table_list" name="havingRequest" value="having" />
		Employee SIN :<input type="number" name="SIN" step="1" required> <br /><br />
			<p><input type="submit" value="Having" name="havingSubmit"></p>
	</form>

	<!-- groupby button -->
	<form method="GET" action="database.php">
		<input type="hidden" id="groupByRequest" name="groupByRequest">
		<p>Get the average rental cost group by equipment type for a given guest’s name and phone number:</p>
		Name: <input type="text" name="guestName" maxlength="50" required> <br /><br />
		Number: <input type="text" name="guestPhoneNumber" maxlength="20" required> <br /><br />
		<input type="submit" value="Group By" name="groupBySubmit"></p>
	</form>

	<!-- join button -->
	<form method="GET" action="database.php">
		<input type="hidden" id="joinRequest" name="joinRequest">
		<p>Retrieves rental information of guest:</p>
		Name: <input type="text" name="guestName" maxlength="50" required> <br /><br />
		Number: <input type="text" name="phoneNumber" maxlength="20" required> <br /><br />
		<p style="margin-top: 0;"><input type="submit" value="Join" name="joinSubmit"></p>
	</form>

	<!-- nested aggregation button -->
	<form method="GET" action="database.php">
		<input type="hidden" id="nestedAggRequest" name="nestedAggRequest">
    <p>Number of guests renting at least one of the most expensive equipment per type:</p>
		<p><input type="submit" value="Nested Aggregation" name="nestedAggSubmit"></p>
	</form>

	<!-- nested division button -->
	<form method="GET" action="database.php">
		<input type="hidden" id="divisionRequest" name="divisionRequest">
    <p>Guests assigned to each employee at least once :</p>
		<p><input type="submit" value="Division" name="divisionSubmit"></p>
	</form>

	<!-- selection -->
	<form method="GET" action="database.php">
    <p>Filter data:</p>
		<input type="hidden" id="selectN" name="selectN">
		Enter number of filters:<input type="number"  name="clauses" min="1" max="10" step="1" required/>
		<input type="submit" value="Choose filters" name="nSubmit">
	</form>


</html>
