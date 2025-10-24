<?php
// Security includes
include 'ip_blocker.php';
include 'auth_check.php';
include 'db_connect.php';

$cnf_agent_id = isset($_GET['cnf_agent_id']) ? (int)$_GET['cnf_agent_id'] : 0;
$prefix = '';
$new_ref_num = 1; // Default to 1

if ($cnf_agent_id > 0) {
    // 1. Get the agent's name to determine the prefix
    $stmt_agent = $conn->prepare("SELECT name FROM cnf_agents WHERE id = ?");
    if ($stmt_agent) {
        $stmt_agent->bind_param("i", $cnf_agent_id);
        $stmt_agent->execute();
        $agent_result = $stmt_agent->get_result();
        if ($agent = $agent_result->fetch_assoc()) {
            if (strpos($agent['name'], 'SHAROTHI') !== false) {
                $prefix = 'TFL/SCM/SE/';
            } elseif (strpos($agent['name'], 'Tea Holdings') !== false) {
                $prefix = 'TFL/SCM/THL/';
            }
        }
        $stmt_agent->close();
    }

    if (!empty($prefix)) {
        // 2. Find the highest existing number for this prefix
        // This query extracts the number after the last '/' and sorts numerically
        $sql_find_max = "SELECT reference_no 
                         FROM proforma_invoices 
                         WHERE reference_no LIKE ? 
                         ORDER BY CAST(SUBSTRING_INDEX(reference_no, '/', -1) AS UNSIGNED) DESC 
                         LIMIT 1";
        
        $stmt_max = $conn->prepare($sql_find_max);
        if ($stmt_max) {
            $like_prefix = $prefix . '%';
            $stmt_max->bind_param("s", $like_prefix);
            $stmt_max->execute();
            $max_result = $stmt_max->get_result();
            
            if ($last_invoice = $max_result->fetch_assoc()) {
                // 3. Increment the number
                $last_ref = $last_invoice['reference_no'];
                // Get the part after the last slash
                $last_num_str = substr($last_ref, strrpos($last_ref, '/') + 1);
                if (is_numeric($last_num_str)) {
                    $new_ref_num = (int)$last_num_str + 1;
                }
            }
            // If no record was found, $new_ref_num remains 1 (the default)
            $stmt_max->close();
        }
    }
}

$conn->close();

// 4. Return the new reference string as JSON
header('Content-Type: application/json');
if (!empty($prefix)) {
    echo json_encode(['success' => true, 'new_ref' => $prefix . $new_ref_num]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid C&F Agent or prefix not found.']);
}
exit();
?>