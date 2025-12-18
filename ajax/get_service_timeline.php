<?php
// 🚨 Шаг 0: Начало — никаких header() пока не убедимся, что всё ОК
$output = ['log' => []];
$output['log'][] = '✅ Script started. PHP ' . PHP_VERSION;

// 🔐 Шаг 1: Подключаем db.php — безопасно
$paths = [
    __DIR__ . '/../functions/db.php',
    dirname(__DIR__) . '/functions/db.php',
    $_SERVER['DOCUMENT_ROOT'] . '/functions/db.php',
    __DIR__ . '/../../functions/db.php'
];

$found = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        try {
            require_once $path;
            $output['log'][] = "✅ db.php loaded from: " . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
            $found = true;
            break;
        } catch (Throwable $e) {
            $output['log'][] = "❌ require db.php failed at $path: " . $e->getMessage();
        }
    }
}
if (!$found) {
    $output['log'][] = "❌ db.php not found in: " . json_encode($paths);
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 🔐 Шаг 2: Подключаем auth.php — безопасно
$auth_found = false;
$auth_paths = [
    __DIR__ . '/../functions/auth.php',
    dirname(__DIR__) . '/functions/auth.php',
    $_SERVER['DOCUMENT_ROOT'] . '/functions/auth.php'
];
foreach ($auth_paths as $path) {
    if (file_exists($path)) {
        try {
            require_once $path;
            $output['log'][] = "✅ auth.php loaded from: " . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
            $auth_found = true;
            break;
        } catch (Throwable $e) {
            $output['log'][] = "❌ require auth.php failed: " . $e->getMessage();
        }
    }
}
if (!$auth_found) {
    $output['log'][] = "❌ auth.php not found";
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 🛡️ Шаг 3: Проверка доступа
try {
    if (!function_exists('check_access')) {
        throw new Exception('check_access() not defined');
    }
    $access_denied = check_access('acl_service', 1);
    $output['log'][] = "✅ check_access() exists. Result: " . ($access_denied ? 'DENIED' : 'ALLOWED');
    if ($access_denied) {
        $output['error'] = 'Access denied (acl_service level 1 required)';
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
        exit;
    }
} catch (Throwable $e) {
    $output['log'][] = "❌ check_access failed: " . $e->getMessage();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 📥 Шаг 4: Чтение входных данных
try {
    $raw_input = file_get_contents('php://input');
    $output['log'][] = "📥 Raw input length: " . strlen($raw_input);
    if (strlen($raw_input) > 0) {
        $output['log'][] = "📥 First 100 chars: " . substr($raw_input, 0, 100);
    }

    $data = json_decode($raw_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON decode: ' . json_last_error_msg());
    }
    $output['log'][] = "✅ JSON parsed. Keys: " . json_encode(array_keys($data ?? []));

    $statuses = $data['statuses'] ?? [];
    $engineers = $data['engineers'] ?? [];
    $output['log'][] = "🔍 Statuses: " . json_encode($statuses);
    $output['log'][] = "👷 Engineers: " . json_encode($engineers);

} catch (Throwable $e) {
    $output['log'][] = "❌ Input parsing failed: " . $e->getMessage();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 🌐 Шаг 5: Подключение к БД
try {
    if (!function_exists('db_connect')) {
        throw new Exception('db_connect() not defined');
    }
    $db = db_connect();
    if (!$db) {
        throw new Exception('db_connect() returned falsy value');
    }
    if (!($db instanceof mysqli)) {
        throw new Exception('db_connect() did not return mysqli instance');
    }
    $output['log'][] = "✅ DB connected. Host: " . $db->host_info;
} catch (Throwable $e) {
    $output['log'][] = "❌ DB connection failed: " . $e->getMessage();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 🧪 Шаг 6: Тестовый запрос — без сложной логики
try {
    $test_query = "SELECT 1 AS ping, DATABASE() AS db";
    $test_res = $db->query($test_query);
    if (!$test_res) {
        throw new Exception('Test query failed: ' . $db->error);
    }
    $test_row = $test_res->fetch_assoc();
    $output['log'][] = "✅ Test query OK. DB: " . ($test_row['db'] ?? 'unknown');
} catch (Throwable $e) {
    $output['log'][] = "❌ Test query failed: " . $e->getMessage();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 📊 Шаг 7: Основной запрос — максимально упрощённый
try {
    // Формируем WHERE — только статусы, без всего остального
    $where = "s.service_deleted = 0";
    $params = [];
    if (!empty($statuses)) {
        $in_list = implode(',', array_map('intval', $statuses));
        $where .= " AND s.status IN ($in_list)";
    }

    $query = "
        SELECT 
            s.service_id,
            'Vessel #' || s.service_id AS vessel_name,
            CURDATE() AS ETA,
            DATE_ADD(CURDATE(), INTERVAL 1 DAY) AS ETD,
            s.status,
            'Engineer' AS engineer_name,
            s.service_id AS vessel_id
        FROM service s
        WHERE $where
        ORDER BY s.service_id DESC
        LIMIT 3
    ";

    $output['log'][] = "🔍 Executing query: " . substr($query, 0, 200) . '...';

    $result = $db->query($query);
    if (!$result) {
        throw new Exception('Main query failed: ' . $db->error . '. Query: ' . $query);
    }

    $items = []; $groups = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => (int)($row['service_id'] ?? 0),
            'content' => $row['engineer_name'] ?? '—',
            'start' => $row['ETA'] ?? date('Y-m-d'),
            'end' => $row['ETD'] ?? date('Y-m-d'),
            'group' => 'v_' . ($row['vessel_id'] ?? $row['service_id']),
            'style' => 'background-color:#4caf50;color:white;padding:4px 8px;'
        ];
        $groups[] = [
            'id' => 'v_' . ($row['vessel_id'] ?? $row['service_id']),
            'content' => htmlspecialchars($row['vessel_name'] ?? 'Unknown') . ' #' . $row['service_id']
        ];
    }

    $output['items'] = $items;
    $output['groups'] = $groups;
    $output['count'] = count($items);
    $output['log'][] = "✅ Query succeeded. Rows: " . count($items);

} catch (Throwable $e) {
    $output['log'][] = "❌ Main query failed: " . $e->getMessage();
    $output['error'] = $e->getMessage();
    http_response_code(500);
}

// 📤 Финал: отправляем ВСЁ
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);