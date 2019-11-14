<?php
/*
	$connection = mysqli_connect('remotemysql.com', 'sUDg9UwluY', 'n6Bgyj1Tu0');
	$database = mysqli_select_db($connection, 'sUDg9UwluY');

	$sql = "SELECT * from lightsensor ORDER BY Date desc LIMIT 10";
	$result = $connection->query($sql);
	while($row = mysqli_fetch_array($result)) {
		echo "<tr><td>";
		echo $row['Date'];
		echo "</td><td>";
		echo $row['Light'];
		echo "</td><td>";
		echo $row['Lux'];
		echo "</td></tr>";
	}
*/
/*
	$connection = mysqli_connect('remotemysql.com', 'sUDg9UwluY', 'n6Bgyj1Tu0');
	$database = mysqli_select_db($connection, 'sUDg9UwluY');

	$sql = "SELECT * from lightsensor ORDER BY Date desc LIMIT 10";
	$result = $connection->query($sql);

	$dbdata = array();

	while ($row = $result->fetch_assoc()) {
		$dbdata[] = $row;
	}

echo json_encode($dbdata);
*/

$servername = "remotemysql.com";
$username = "sUDg9UwluY";
$password = "n6Bgyj1Tu0";
$dbname = "sUDg9UwluY";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
/* Database connection end */
// storing  request (ie, get/post) global array to a variable
$requestData= $_REQUEST;
$columns = array(
// datatable column index  => database column name
	0 =>'Date',
	1 => 'Light',
	2=> 'Lux'
);
// getting total number records without any search
$sql = "SELECT Date, Light, Lux ";
$sql.=" FROM lightsensor";
$query=mysqli_query($conn, $sql) or die("test.php: get lightsensor");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT Date, Light, Lux ";
	$sql.=" FROM lightsensor";
	$sql.=" WHERE Date LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR Light LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR Lux LIKE '".$requestData['search']['value']."%' ";
	$query=mysqli_query($conn, $sql) or die("test.php: get lightsensor");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query = mysqli_query($conn, $sql) or die("test.php: get lightsensor"); // again run query with limit

} else {
	$sql = "SELECT Date, Light, Lux ";
	$sql.=" FROM lightsensor";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = mysqli_query($conn, $sql) or die("test.php: get lightsensor");

}
$data = array();
while($row = mysqli_fetch_array($query)) {  // preparing an array
	$nestedData = array();
	$nestedData[] = $row["Date"];
	$nestedData[] = $row["Light"];
	$nestedData[] = $row["Lux"];

	$data[] = $nestedData;
}
$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);
echo json_encode($json_data);  // send data as json format
?>
