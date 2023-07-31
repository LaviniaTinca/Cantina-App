<?php
// include '../php/connection.php';

// // Get the total number of records in the table
// $totalRecords = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

// // Get the search keyword entered by the user
// $searchValue = $_GET['search']['value'];

// // SQL query to fetch filtered and paginated data
// $query = "SELECT * FROM users WHERE name LIKE '%$searchValue%' OR email LIKE '%$searchValue%' LIMIT :start, :length";
// $statement = $conn->prepare($query);

// // Calculate the start and length for pagination
// $start = $_GET['start'];
// $length = $_GET['length'];

// // Bind parameters and execute the query
// $statement->bindParam(':start', $start, PDO::PARAM_INT);
// $statement->bindParam(':length', $length, PDO::PARAM_INT);
// $statement->execute();
// $filteredData = $statement->fetchAll(PDO::FETCH_ASSOC);

// // Prepare the response data for DataTables
// $response = [
//     'draw' => intval($_GET['draw']),
//     'recordsTotal' => $totalRecords,
//     'recordsFiltered' => count($filteredData),
//     'data' => $filteredData,
// ];

// echo json_encode($response);

//MERGE DOAR PARTIAL, GASESTE CA SUNT MULTE ENTRY-URI DAR FILTREAZA DOAR CELE SELECTATE DEJA PE PAGINA
include '../php/connection.php';

// Get the total number of records in the table
$totalRecords = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Get the search keyword entered by the user
$searchValue = $_GET['search']['value'];

// Get the current page number from DataTables
$page = isset($_GET['start']) ? ($_GET['start'] / $_GET['length'] + 1) : 1;

// Calculate the offset based on the current page and number of records per page
$offset = ($_GET['length'] * ($page - 1));

// SQL query to fetch filtered and paginated data
$query = "SELECT * FROM users WHERE name LIKE '%$searchValue%' OR email LIKE '%$searchValue%' LIMIT :start, :length";
$statement = $conn->prepare($query);

// Bind parameters and execute the query
$statement->bindParam(':start', $offset, PDO::PARAM_INT);
$statement->bindParam(':length', $_GET['length'], PDO::PARAM_INT);
$statement->execute();
$filteredData = $statement->fetchAll(PDO::FETCH_ASSOC);

// Prepare the response data for DataTables
$response = [
    'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => count($filteredData),
    'data' => $filteredData,
];

echo json_encode($response);
