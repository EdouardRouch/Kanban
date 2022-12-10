<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../config/response.php';

// instantiate database and product object
$database = new Database();
$conn = $database->getConnection();

// initialize object
$user = new User($conn);

// query products
try {
    $stmt = $user->read();
} catch (Exception $exception) {
    http_response_code(503);
    $res = new ResponseBody("Opération impossible : service non disponnible");
    echo json_encode($res);
    die;
}
$num = $stmt->rowCount();

// check if more than 0 record found
if ($num > 0) {

    // products array
    $res = new ResponseBody("", array());

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $user_item = array(
            "name" => $name,
        );

        array_push($res->records, $user_item);
    }

    // set response code - 200 OK
    http_response_code(200);
    // show products data in json format
    echo json_encode($res);
} else {

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        new ResponseBody("Aucun utilisteur trouvé")
    );
}
