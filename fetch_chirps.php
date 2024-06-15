<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 6;

    $query = 'SELECT * FROM chirps ORDER BY timestamp DESC LIMIT :limit OFFSET :offset';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['chirp'] = nl2br(htmlspecialchars($row['chirp']));
        $chirps[] = $row;
    }

    echo json_encode($chirps);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>