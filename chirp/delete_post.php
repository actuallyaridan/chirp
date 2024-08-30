<?php 
try {
    session_start();
    $userId = $_SESSION['user_id'];
    $id = $_REQUEST["chirp"];
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "SELECT user FROM chirps WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    if ($stmt->fetchColumn() == $userId) {
        //$sql = "DELETE FROM chirps WHERE id = :chirpId";
        //OR to edit to show This chirp is not available
        $sql = "UPDATE chirps SET chirp = 'This chirp is not available.' WHERE id = :chirpId";
        $stm = $db->prepare($sql);
        $stm->bindParam(':chirpId', $id);     
        //$stm->bindParam(':newContent', "This chirp is not available.");   
        $stm->execute();
        http_response_code(200); // Set HTTP response code to 500 for server error
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Set HTTP response code to 500 for server error
        echo json_encode(['error' => 'You are not allowed to perform that action.']);
    }
    } catch (PDOException $e) {
        // Log error details for debugging purposes
        error_log($e->getMessage());
        http_response_code(500); // Set HTTP response code to 500 for server error
        echo json_encode(['error' => 'An error occoured while deleting that post.']);
    }
?>
