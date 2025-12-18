<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * service_timeline.php
 * AJAX-эндпоинт для загрузки данных таймлайна заявок (Vis.js Timeline).
 */

// 🔹 Загрузка зависимостей
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/service.php';

// Установка заголовка JSON в самом начале
header('Content-Type: application/json; charset=utf-8');

// ===================================================================
// 🔒 1. Проверка доступа
// ===================================================================
if (check_access('acl_service', 1)) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===================================================================
// 🛠 2. Подключение к БД
// ===================================================================
$db = db_connect();
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===================================================================
// 📥 3. Получение и валидация входных данных
// ===================================================================
function safeJsonDecode($jsonStr, $validator, $transformer) {
    if (empty($jsonStr) || $jsonStr === '[]') {
        return [];
    }
    
    $decoded = json_decode($jsonStr, true);
    if (!is_array($decoded)) {
        return [];
    }
    
    $result = [];
    foreach ($decoded as $item) {
        $transformed = $transformer($item);
        if ($validator($transformed)) {
            $result[] = $transformed;
        }
    }
    return $result;
}

// Получаем и валидируем параметры
$status = safeJsonDecode(
    $_POST['status'] ?? '[]',
    fn($v) => is_int($v) && $v > 0,
    fn($v) => (int)$v
);

$users = safeJsonDecode(
    $_POST['users'] ?? '[]',
    fn($v) => is_int($v) && $v > 0,
    fn($v) => (int)$v
);

$companies = safeJsonDecode(
    $_POST['companies'] ?? '[]',
    fn($v) => is_int($v) && $v > 0,
    fn($v) => (int)$v
);

$period = $_POST['period'] ?? '1m';
$periodStart = trim($_POST['period_start'] ?? '');
$periodEnd   = trim($_POST['period_end']   ?? '');

// Валидация кастомного диапазона
if ($period === 'custom' && $periodStart !== '' && $periodEnd !== '') {
    $periodStart = preg_replace('/[^0-9\-]/', '', $periodStart);
    $periodEnd   = preg_replace('/[^0-9\-]/', '', $periodEnd);

    $isValidDate = function($date) {
        return preg_match('/^\d{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])$/', $date) &&
               (new DateTime($date))->format('Y-m-d') === $date;
    };

    if (!$isValidDate($periodStart) || !$isValidDate($periodEnd)) {
        $periodStart = $periodEnd = '';
    } elseif ($periodStart > $periodEnd) {
        [$periodStart, $periodEnd] = [$periodEnd, $periodStart];
    }
}

// ===================================================================
// 📅 4. Формирование диапазона дат
// ===================================================================
$now = new DateTime();

switch ($period) {
    case 'custom':
        if ($periodStart && $periodEnd) {
            $startDate = $periodStart;
            $endDate   = $periodEnd;
        } else {
            $startDate = (clone $now)->modify('first day of -1 month')->format('Y-m-d');
            $endDate   = (clone $now)->modify('last day of +1 month')->format('Y-m-d');
        }
        break;

    case '3m':
        $startDate = (clone $now)->modify('first day of -3 months')->format('Y-m-d');
        $endDate   = (clone $now)->modify('last day of +3 months')->format('Y-m-d');
        break;

    case '6m':
        $startDate = (clone $now)->modify('first day of -6 months')->format('Y-m-d');
        $endDate   = (clone $now)->modify('last day of +6 months')->format('Y-m-d');
        break;

    case '1y':
        $startDate = (clone $now)->modify('first day of -1 year')->format('Y-m-d');
        $endDate   = (clone $now)->modify('last day of +1 year')->format('Y-m-d');
        break;

    case '1m':
    default:
        $startDate = (clone $now)->modify('first day of -1 month')->format('Y-m-d');
        $endDate   = (clone $now)->modify('last day of +1 month')->format('Y-m-d');
        break;
}

// ===================================================================
// 🧠 5. Определение статусов (по разметке)
// ===================================================================
$activeStatuses = [1, 2, 3, 6, 7, 9]; 
$visibleStatuses    = [1, 2, 3, 6, 7, 9];
$excludeStatuses    = [5];

$activeStatusesStr  = implode(',', $activeStatuses);
$visibleStatusesStr = implode(',', $visibleStatuses);

// ===================================================================
// 🧱 6. ФОРМИРОВАНИЕ SQL-ЗАПРОСА
// ===================================================================
$vesselsInPeriodSubquery = "
    SELECT DISTINCT s.vessel_id
    FROM service s
    WHERE s.service_deleted = 0
      AND s.status IN ($activeStatusesStr)
      AND (
          (s.ETA IS NOT NULL AND s.ETA BETWEEN '$startDate' AND '$endDate')
          OR
          (s.ETD IS NOT NULL AND s.ETD BETWEEN '$startDate' AND '$endDate')
      )
";

$where = [
    's.service_deleted = 0',
    's.status NOT IN (5)',
    "s.vessel_id IN ($vesselsInPeriodSubquery)"
];

// Фильтр по статусам
if (!empty($status)) {
    $allowed = array_intersect($status, $visibleStatuses);
    if (empty($allowed)) {
        echo json_encode(['items' => [], 'groups' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $where[] = 's.status IN (' . implode(',', $allowed) . ')';
}

// Фильтр по пользователям
if (!empty($users)) {
    $where[] = 'su.su_uid IN (' . implode(',', $users) . ')';
}

// Фильтр по компаниям
if (!empty($companies)) {
    $where[] = 's.service_our_comp IN (' . implode(',', $companies) . ')';
}

$whereClause = 'WHERE ' . implode(' AND ', $where);

$query = "
    SELECT 
        s.service_id,
        s.service_no,
        s.service_our_comp,
        oc.our_name AS company_name,
        s.vessel_id,
        COALESCE(v.vessel_name, '—') AS vessel_name,
        s.status,
        s.ETA,
        s.ETD,
        s.description AS customer,
        GROUP_CONCAT(u.full_name SEPARATOR ', ') AS engineers,
        GROUP_CONCAT(su.su_uid) AS executor_ids,
        CASE WHEN s.vessel_id IS NOT NULL THEN (
            SELECT MIN(COALESCE(ss.ETA, ss.ETD))
            FROM service ss
            WHERE ss.vessel_id = s.vessel_id
              AND ss.service_deleted = 0
              AND ss.status IN ($activeStatusesStr)
        ) END AS vessel_min_active_date,
        CASE WHEN s.vessel_id IS NOT NULL THEN (
            SELECT MAX(ss.service_id)
            FROM service ss
            WHERE ss.vessel_id = s.vessel_id
              AND ss.service_deleted = 0
              AND ss.status NOT IN (5)
        ) END AS vessel_max_service_id
    FROM service s
    LEFT JOIN vessels v        ON v.vessel_id = s.vessel_id
    LEFT JOIN service_users su ON su.su_service_id = s.service_id
    LEFT JOIN users u          ON u.uid = su.su_uid
    LEFT JOIN our_companies oc ON s.service_our_comp = oc.id
    $whereClause
    GROUP BY 
        s.service_id, s.service_no, s.service_our_comp, oc.our_name,
        s.vessel_id, v.vessel_name, s.status, s.ETA, s.ETD, s.description
    ORDER BY 
        vessel_min_active_date IS NULL,
        vessel_min_active_date ASC,
        vessel_max_service_id DESC,
        FIELD(s.status, 1, 2, 3, 7, 6, 9) ASC,
        COALESCE(s.ETA, s.ETD, '9999-12-31') ASC,
        s.service_id DESC
";

$result = $db->query($query);
if (!$result) {
    error_log("SQL Error (service_timeline): " . $db->error . " | Query: " . $query);
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===================================================================
// 🗺 7. Формирование items & groups
// ===================================================================
function getStatusClass(int $statusId): string {
    $map = [
        1 => 'status_request',
        2 => 'status_quotation',
        3 => 'status_confirmed',
        5 => 'status_canceled',
        6 => 'status_complited',
        7 => 'status_follow-up',
        8 => 'status_expired',
        9 => 'status_post-processing',
    ];
    return $map[$statusId] ?? 'status_unknown';
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

// 🔹 Добавляем флаг is_contextual
foreach ($rows as &$row) {
    $hasDate = !empty($row['ETA']) || !empty($row['ETD']);
    $isContextual = false;
    $isOutOfPeriod = false; // ← Флаг для ЛЮБОЙ активной заявки с датами ВНЕ периода
    $contextualETA = null;
    $contextualETD = null;

    $statusId = (int)($row['status'] ?? 0);
    
    // Проверяем, является ли заявка активной с датами ВНЕ периода
    if (in_array($statusId, $activeStatuses) && $hasDate) {
        $ownETA = $row['ETA'];
        $ownETD = $row['ETD'];
        
        // Проверяем, что собственные даты заявки ВНЕ выбранного периода
        $etaInPeriod = $ownETA && $ownETA >= $startDate && $ownETA <= $endDate;
        $etdInPeriod = $ownETD && $ownETD >= $startDate && $ownETD <= $endDate;
        
        if (!$etaInPeriod && !$etdInPeriod) {
            $isOutOfPeriod = true;
        }
    }

    // Контекстными считаем: либо нет дат, либо даты ВНЕ периода
    if (!$hasDate || $isOutOfPeriod) {
        $vid = (int)($row['vessel_id'] ?? 0);
        $sid = (int)($row['service_id'] ?? 0);
        if ($vid > 0) {
            // Ищем активные заявки этого судна с датами в периоде
            $stmt = $db->prepare("
                SELECT s.ETA, s.ETD 
                FROM service s
                WHERE s.vessel_id = ?
                  AND s.service_id != ?
                  AND s.service_deleted = 0
                  AND s.status IN ($activeStatusesStr)
                  AND (s.ETA BETWEEN ? AND ? OR s.ETD BETWEEN ? AND ?)
                LIMIT 1
            ");
            if ($stmt) {
                $stmt->bind_param('iissss', $vid, $sid, $startDate, $endDate, $startDate, $endDate);
                $stmt->execute();
                $stmt->bind_result($contextETA, $contextETD);
                if ($stmt->fetch()) {
                    $isContextual = true;
                    $contextualETA = $contextETA;
                    $contextualETD = $contextETD;
                }
                $stmt->close();
            }
        }
    }
    
    $row['is_contextual'] = $isContextual;
    $row['is_out_of_period'] = $isOutOfPeriod; // ← Сохраняем флаг "даты ВНЕ периода"
    $row['contextual_eta'] = $contextualETA;
    $row['contextual_etd'] = $contextualETD;
}
unset($row);

// Подготовка сортировки
$processed = [];
foreach ($rows as $row) {
    $vid = $row['vessel_id'] ?? null;
    $sid = (int)$row['service_id'];
    $status = (int)($row['status'] ?? 0);

    $groupDate = $row['vessel_min_active_date'] ?? '9999-12-31';
    $groupFallbackId = (int)($row['vessel_max_service_id'] ?? 0);
    $groupOrder = $groupDate !== '9999-12-31'
        ? $groupDate
        : sprintf('9999-12-31_%010d', -$groupFallbackId);

    $statusPriority = array_search($status, [1, 2, 3, 7, 6, 9]) ?: 99;
    $dateForSort = $row['ETA'] ?: $row['ETD'] ?: '9999-12-31';
    $subOrder = sprintf('%02d_%s_%010d', $statusPriority, $dateForSort, -$sid);

    $row['_sortGroup'] = $groupOrder;
    $row['_sortSub']   = $subOrder;
    $processed[] = $row;
}

usort($processed, function ($a, $b) {
    $g = strcmp($a['_sortGroup'], $b['_sortGroup']);
    return $g !== 0 ? $g : strcmp($a['_sortSub'], $b['_sortSub']);
});

$items = [];
$groups = [];

foreach ($processed as $row) {
    $serviceId    = (int) $row['service_id'];
    $vesselId     = $row['vessel_id'] ?? null;
    $vesselName   = htmlspecialchars($row['vessel_name'] ?? '—', ENT_QUOTES, 'UTF-8');
    $serviceNo    = $row['service_no'] ?? null;
    $statusId     = (int) ($row['status'] ?? 0);
    $eta          = $row['ETA'];
    $etd          = $row['ETD'];
    $customer     = htmlspecialchars($row['customer'] ?? '', ENT_QUOTES, 'UTF-8');
    $engineers    = htmlspecialchars($row['engineers'] ?? '', ENT_QUOTES, 'UTF-8');
    $companyName  = htmlspecialchars($row['company_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $isContextual = $row['is_contextual'];
    $contextualETA = $row['contextual_eta'];
    $contextualETD = $row['contextual_etd'];

    $groupId = "srv_{$serviceId}";

    // Формируем content
    $contentParts = [];
    if (!empty($customer)) {
        $contentParts[] = $customer;
    }
    if (!empty($engineers)) {
        $contentParts[] = "({$engineers})";
    }
    $contentBase = trim(implode(' ', $contentParts));

    // 🔹 ОПРЕДЕЛЯЕМ ТИП БЕЙДЖА
    $badge = '';
    if ($isContextual) {
        if ($row['is_out_of_period']) { // ← используем значение из массива
            $badge = ' <span class="timeline-context-badge timeline-out-of-period")">[Вне периода]</span>';
        } else {
            $badge = ' <span class="timeline-context-badge">[Нет ETA, ETD]</span>';
        }
    }

$content = $contentBase . $badge;

$content = $contentBase . $badge;
    

    $groups[] = [
        'id'              => $groupId,
        'vesselName'      => $vesselName,
        'vesselId'        => $vesselId,
        'serviceId'       => $serviceId,
        'serviceNo'       => $serviceNo,
        'serviceOurComp'  => $companyName,
        'status'          => $statusId,
        'sortKey'         => $row['_sortGroup'] . '_' . $row['_sortSub'],
        'is_contextual'   => $isContextual,
    ];

    // 🔹 ОПРЕДЕЛЯЕМ ДАТЫ ДЛЯ ОТРИСОВКИ
    $start = null;
    $end = null;
    $isDot = false;
    $className = getStatusClass($statusId);

    if ($isContextual) {
        // 🔥 ДЛЯ ЗАЯВОК С [Визит] - используем даты из активной заявки
        $start = $contextualETA ?: $contextualETD;
        $end = $contextualETD ?: $contextualETA;
        
        // Если есть обе даты - это диапазон, если одна - точка
        $isDot = empty($contextualETA) || empty($contextualETD);
        
        // Добавляем класс для пунктирной обводки
        $className .= $isDot ? ' service_dot service_contextual' : ' service_contextual';
        
    } else {
        // Обычные заявки
        $isDot = empty($eta) && empty($etd);
        $start = $eta ?: $etd ?: date('Y-m-d');
        $end = $isDot ? null : ($etd ?: $eta);
        
        if ($isDot) {
            $className .= ' service_dot';
        }
    }

    $items[] = [
        'id'              => "item_{$serviceId}",
        'group'           => $groupId,
        'content'         => $content,
        'start'           => $start,
        'end'             => $end,
        'className'       => $className,
        'type'            => $isDot ? 'point' : 'range',
        'vesselName'      => $vesselName,
        'serviceId'       => $serviceId,
        'serviceOurComp'  => $companyName,
        'is_contextual'   => $isContextual,
        'customer'        => $customer,
    ];
}

// ===================================================================
// 📤 8. Возврат результата
// ===================================================================
$response = [
    'items' => $items,
    'groups' => $groups
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;