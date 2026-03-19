<?php

function get_skus() {
    global $connection;
    $result = $connection->query("SELECT * FROM ram_skus ORDER BY sku");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_sku($id) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_skus WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_sku_by_code($code) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_skus WHERE sku = ? LIMIT 1");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function insert_sku($data) {
    global $connection;
    $stmt = $connection->prepare(
        "INSERT INTO ram_skus (ficha, sku, description, uom_primary, piece_count,
        length_inches, width_inches, height_inches, weight_lbs, assembly, rate)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $ficha = $data['ficha']         ?? 0;
    $pc    = $data['piece_count']   ?? 0;
    $l     = $data['length_inches'] ?? 0;
    $w     = $data['width_inches']  ?? 0;
    $h     = $data['height_inches'] ?? 0;
    $wt    = $data['weight_lbs']    ?? 0;
    $rate  = $data['rate']          ?? 0;
    $stmt->bind_param('isssiddddsd',
        $ficha, $data['sku'], $data['description'], $data['uom_primary'],
        $pc, $l, $w, $h, $wt, $data['assembly'], $rate
    );
    if ($stmt->execute()) {
        return $connection->insert_id;
    }
    return false;
}

function save_sku($id, $data) {
    global $connection;
    $stmt = $connection->prepare(
        "UPDATE ram_skus SET ficha=?, sku=?, description=?, uom_primary=?, 
        piece_count=?, length_inches=?, width_inches=?, height_inches=?, weight_lbs=?, 
        assembly=?, rate=? WHERE id=?"
    );
    $ficha = $data['ficha']         ?? 0;
    $pc    = $data['piece_count']   ?? 0;
    $l     = $data['length_inches'] ?? 0;
    $w     = $data['width_inches']  ?? 0;
    $h     = $data['height_inches'] ?? 0;
    $wt    = $data['weight_lbs']    ?? 0;
    $rate  = $data['rate']          ?? 0;
    $stmt->bind_param('isssiddddsdi',
        $ficha, $data['sku'], $data['description'], $data['uom_primary'],
        $pc, $l, $w, $h, $wt, $data['assembly'], $rate, $id
    );
    return $stmt->execute();
}

function remove_sku($id) {
    global $connection;
    $stmt = $connection->prepare("DELETE FROM ram_skus WHERE id = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
}

function get_inventory() {
    global $connection;
    $sql = "SELECT i.*, s.sku AS sku_code, s.description, s.uom_primary,
            s.piece_count, s.length_inches, s.width_inches, s.height_inches,
            s.weight_lbs, s.assembly, s.rate
            FROM ram_inventory i JOIN ram_skus s ON i.sku_id = s.id
            ORDER BY i.created_at DESC";
    $result = $connection->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function add_inventory_unit($unit_id, $sku_code) {
    global $connection;
    $sku = get_sku_by_code($sku_code);
    if (!$sku) {
        return false;
    }
    $stmt = $connection->prepare("INSERT INTO ram_inventory (unit_id, sku_id) VALUES (?, ?)");
    $stmt->bind_param('si', $unit_id, $sku['id']);
    return $stmt->execute();
}

function remove_inventory_unit($unit_id) {
    global $connection;
    $stmt = $connection->prepare("DELETE FROM ram_inventory WHERE unit_id = ?");
    $stmt->bind_param('s', $unit_id);
    return $stmt->execute();
}

function get_mpls() {
    global $connection;
    $result = $connection->query("SELECT * FROM ram_mpls ORDER BY created_at DESC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_mpl($id) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_mpls WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_mpl_by_reference($ref) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_mpls WHERE reference_number = ? LIMIT 1");
    $stmt->bind_param('s', $ref);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_mpl_items($mpl_id) {
    global $connection;
    $stmt = $connection->prepare(
        "SELECT mi.*, s.description, s.uom_primary
        FROM ram_mpl_items mi 
        LEFT JOIN ram_skus s 
        ON mi.sku = s.sku
        WHERE mi.mpl_id = ?"
    );
    $stmt->bind_param('i', $mpl_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function save_mpl($data, $items) {
    global $connection;
    $stmt = $connection->prepare(
        "INSERT INTO ram_mpls (reference_number, trailer_number, expected_arrival, status)
        VALUES (?, ?, ?, 'open')"
    );
    $stmt->bind_param('sss',
        $data['reference_number'],
        $data['trailer_number'],
        $data['expected_arrival']
    );
    $stmt->execute();
    $mpl_id = $connection->insert_id;

    $stmt = $connection->prepare("INSERT INTO ram_mpl_items (mpl_id, unit_id, sku) VALUES (?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param('iss', $mpl_id, $item['unit_id'], $item['sku']);
        $stmt->execute();
    }
    return $mpl_id;
}

function close_mpl($id) {
    global $connection;
    $stmt = $connection->prepare("UPDATE ram_mpls SET status = 'closed' WHERE id = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
}

function get_orders() {
    global $connection;
    $result = $connection->query("SELECT * FROM ram_orders ORDER BY created_at DESC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_order($id) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_orders WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_order_by_number($num) {
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM ram_orders WHERE order_number = ? LIMIT 1");
    $stmt->bind_param('s', $num);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_order_items($order_id) {
    global $connection;
    $stmt = $connection->prepare(
        "SELECT oi.*, s.description, s.uom_primary
        FROM ram_order_items oi 
        LEFT JOIN ram_skus s 
        ON oi.sku = s.sku
        WHERE oi.order_id = ?"
    );
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function save_order($data, $items) {
    global $connection;
    $stmt = $connection->prepare(
        "INSERT INTO ram_orders (order_number, ship_to_company, ship_to_street,
        ship_to_city, ship_to_state, ship_to_zip, status)
        VALUES (?, ?, ?, ?, ?, ?, 'open')"
    );
    $stmt->bind_param('ssssss',
        $data['order_number'], $data['ship_to_company'], $data['ship_to_street'],
        $data['ship_to_city'], $data['ship_to_state'],  $data['ship_to_zip']
    );
    $stmt->execute();
    $order_id = $connection->insert_id;

    $stmt = $connection->prepare("INSERT INTO ram_order_items (order_id, unit_id, sku) VALUES (?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param('iss', $order_id, $item['unit_id'], $item['sku']);
        $stmt->execute();
    }
    return $order_id;
}

function ship_order($id, $shipped_at) {
    global $connection;
    $stmt = $connection->prepare("UPDATE ram_orders SET status = 'closed', shipped_at = ? WHERE id = ?");
    $stmt->bind_param('si', $shipped_at, $id);
    return $stmt->execute();
}

function record_shipped_items($order_id, $order_number, $items, $shipped_at) {
    global $connection;
    $stmt = $connection->prepare(
        "INSERT INTO ram_shipped_items (order_id, order_number, unit_id, sku, sku_description, shipped_at)
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    foreach ($items as $item) {
        $desc = $item['description'] ?? '';
        $stmt->bind_param('isssss',
            $order_id, $order_number,
            $item['unit_id'], $item['sku'], $desc, $shipped_at
        );
        $stmt->execute();
    }
}

function get_shipped_summary() {
    global $connection;
    $sql = "SELECT o.id, o.order_number, o.ship_to_company, o.ship_to_city,
            o.ship_to_state, o.shipped_at, COUNT(si.id) AS item_count
            FROM ram_orders o JOIN ram_shipped_items si ON o.id = si.order_id
            WHERE o.status = 'closed'
            GROUP BY o.id
            ORDER BY o.shipped_at DESC";
    $result = $connection->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function get_shipped_items($order_id) {
    global $connection;
    $stmt = $connection->prepare(
        "SELECT * FROM ram_shipped_items WHERE order_id = ? ORDER BY unit_id"
    );
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}
