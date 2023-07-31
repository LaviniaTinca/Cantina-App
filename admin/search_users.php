<?php
// include '../php/connection.php';
// try {
//     if (isset($_POST['keyword'])) {
//         $keyword = $_POST['keyword'];

//         $query = "SELECT * FROM users WHERE 
//                   name LIKE CONCAT('%', :keyword, '%') OR 
//                   email LIKE CONCAT('%', :keyword, '%') OR 
//                   user_type LIKE CONCAT('%', :keyword, '%')";

//         $stmt = $conn->prepare($query);
//         $stmt->bindParam(':keyword', $keyword);
//         $stmt->execute();
//         $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         echo json_encode($results);
//     } else {
//         echo json_encode(array('error' => 'Keyword not provided'));
//     }
// } catch (PDOException $e) {
//     echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
// }
include '../php/connection.php';

// Get the total number of records in the table
$totalRecords = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Get the search keyword entered by the user
$searchValue = $_GET['search']['value'];

// Get the column to sort and order
$columns = ['name', 'email', 'user_type'];
$orderBy = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 0;
$orderBy = ($orderBy >= 0 && $orderBy < count($columns)) ? $columns[$orderBy] : $columns[0];

$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

// Get the current page number from DataTables
$page = isset($_GET['start']) ? ($_GET['start'] / $_GET['length'] + 1) : 1;

// Calculate the offset based on the current page and number of records per page
$limit = isset($_GET['length']) ? $_GET['length'] : 10;
$offset = ($page - 1) * $limit;

// SQL query to fetch filtered and paginated data
$query = "SELECT * FROM users WHERE name LIKE '%$searchValue%' OR email LIKE '%$searchValue%' ORDER BY $orderBy $orderDir LIMIT :limit OFFSET :offset";
$statement = $conn->prepare($query);

// Bind parameters and execute the query
$statement->bindParam(':limit', $limit, PDO::PARAM_INT);
$statement->bindParam(':offset', $offset, PDO::PARAM_INT);
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
