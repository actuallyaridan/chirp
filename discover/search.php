<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if ($query !== '') {
        $searchQuery = '%' . $query . '%';
        $sql = 'SELECT * FROM chirps WHERE chirp LIKE :query ORDER BY timestamp DESC';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    } else {
        $sql = 'SELECT * FROM chirps ORDER BY timestamp DESC';
        $stmt = $db->prepare($sql);
    }

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