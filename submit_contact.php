<?php
header('Content-Type: application/json');

try {
    // Connect to the MySQL database with PDO
    $pdo = new PDO('mysql:host=localhost;dbname=contact_form_db', 'root', ''); // Replace with your actual credentials
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read JSON input
    $body = file_get_contents('php://input');
    $request = json_decode($body, true);

    // Prepare and execute the insert query for the `contacts` table
    $query = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)");
    $query->bindValue(':name', $request['nama'], PDO::PARAM_STR);
    $query->bindValue(':email', $request['email'], PDO::PARAM_STR);
    $query->bindValue(':message', $request['message'], PDO::PARAM_STR);
    $result = $query->execute();

    if ($result) {
        // Retrieve the newly inserted record to confirm
        $id = $pdo->lastInsertId();
        $query = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $contact = $query->fetch(PDO::FETCH_ASSOC);
        echo json_encode($contact);
    } else {
        throw new Exception("Failed to save data");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
