<?php
	$database = "to_do_list";
    $host = "127.0.0.1";
    $port = "3306";
    $user = "root";
    $pass = "";
    $dsn = "mysql:dbname={$database};host={$host};port={$port}";
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    $connection = new PDO($dsn, $user, $pass, $options);

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    // Подсмотрел такую реализацию CRUD у GPT, про swich - case знаю еще с питона, очень рад что он есть и тут :)
    switch ($_SERVER['REQUEST_METHOD']) {

        // Чтение из БД
        case 'GET':
            $sql = "SELECT * FROM `categories`";
            $query = $connection->query($sql);
            $categories = $query->fetchAll();
            // Ответ в fe
            echo json_encode([
                'categories' => $categories
            ]);
            exit;

        // Добавление в БД новой строки
        case 'POST':
            // проверка есть ли title
            if (!isset($data['title'])) {
                http_response_code(400);
                echo json_encode(["error" => "title is required"]);
                exit;
            }
            $sql = "INSERT INTO categories (title)
                    VALUES (:title)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':title' => $data['title']
            ]);
            // Ответ в fe
            echo json_encode(["status" => "created"]);
            break;

        // Изменение значения в БД
        case 'PUT':
            $sql = "UPDATE categories 
                    SET title=:title
                    WHERE id=:id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':title' => $data['title'],
                ':id' => $data['id']
            ]);
            // Ответ в fe
            echo json_encode(["status" => "updated"]);
            break;
            
        // Удаление значения из БД
        case 'DELETE':
            $sql = "DELETE FROM categories WHERE id=:id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':id' => $data['id']
            ]);
            // Ответ в fe
            echo json_encode(["status" => "deleted"]);
            break;
    }

    
