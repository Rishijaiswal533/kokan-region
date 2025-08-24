<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$user = "root";     // change if needed
$pass = "";         // change if needed
$db   = "v2.thirdi.app";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

// Which dataset to return
$type = $_GET['type'] ?? 'major_minor';

// Switch between queries for each chart
switch ($type) {
    case "major_minor":
        $sql = "SELECT major_head, minor_head, COUNT(*) as total
                FROM cctns_crime_records
                GROUP BY major_head, minor_head";
        break;

    case "year_station":
        $sql = "SELECT YEAR(reporting_date) as year, police_station_name, COUNT(*) as total
                FROM cctns_crime_records
                GROUP BY year, police_station_name
                ORDER BY year ASC";
        break;

    case "caste":
        $sql = "SELECT complainant_caste as label, COUNT(*) as total
                FROM cctns_crime_records
                GROUP BY complainant_caste";
        break;

    case "gender":
        $sql = "SELECT complainant_gender as label, COUNT(*) as total
                FROM cctns_crime_records
                GROUP BY complainant_gender";
        break;

    case "religion":
        $sql = "SELECT complainant_religion as label, COUNT(*) as total
                FROM cctns_crime_records
                GROUP BY complainant_religion";
        break;

    case "age":
        $sql = "SELECT complainant_age as age, major_head
                FROM cctns_crime_records
                WHERE complainant_age IS NOT NULL";
        break;

    default:
        echo json_encode(["error" => "Invalid type"]);
        exit;
}

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$conn->close();

echo json_encode($data);
