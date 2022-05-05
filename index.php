<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');


include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM users";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($users);
        break;
    case "POST":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO users(id, name, email, mobile, create_at) VALUES(null, :name, :email, :mobile, :create_at)";
        $stmt = $conn->prepare($sql);
        $create_at = date('Y-m-d');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':mobile', $user->mobile);
        $stmt->bindParam(':create_at', $create_at);
        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        return json_encode($response);
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE users SET name = :name, email = :email, mobile = :mobile, update_at = :update_at WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $create_at = date('Y-m-d');
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':mobile', $user->mobile);
        $stmt->bindParam(':update_at', $update_at);
        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        return json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);
        $stmt->execute();
        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        return json_encode($response);
        break;
}

