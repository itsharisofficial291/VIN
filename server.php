<?php
// Database connection details
$serverName = "localhost"; // Use "localhost" if MySQL is running on the same server
$connectionOptions = array(
    "Database" => "vin",    // Name of your database
    "Uid" => "root",        // Username for accessing the database
    "PWD" => ""             // Password for the database user
);

// Connect to the database
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Get the VIN and optional year from the request
$vin = isset($_GET['vin']) ? $_GET['vin'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Validate VIN
if (empty($vin)) {
    echo "VIN is required.";
    exit;
}

// Execute the stored procedure
$sql = "{CALL dbo.spVinDecode(?, ?)}";
$params = array($vin, $year);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the result
$result = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $result[] = $row;
}

// Return result as XML
header("Content-Type: text/xml");
echo '<response>';
foreach ($result as $item) {
    echo '<item>';
    foreach ($item as $key => $value) {
        echo "<{$key}>{$value}</{$key}>";
    }
    echo '</item>';
}
echo '</response>';

// Close connection
sqlsrv_close($conn);
?>
