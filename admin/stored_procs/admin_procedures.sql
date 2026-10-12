-- ===================================================
-- NimbusDesk: Admin Stored Procedures
-- Purpose: Handlers specifically for IT System Administrators & Supervisors
-- ===================================================

USE `nimbusdesk`;

DROP PROCEDURE IF EXISTS `sp_admin_get_all_tickets`;
DROP PROCEDURE IF EXISTS `sp_admin_assign_ticket`;
DROP PROCEDURE IF EXISTS `sp_admin_update_ticket`;
DROP PROCEDURE IF EXISTS `sp_admin_get_dashboard_stats`;
DROP PROCEDURE IF EXISTS `sp_admin_get_incident_history`;

DELIMITER $$

-- 1. Retrieve all tickets with comprehensive filters
CREATE PROCEDURE `sp_admin_get_all_tickets`(
    IN p_status_filter VARCHAR(20),
    IN p_category_filter VARCHAR(50),
    IN p_urgency_filter VARCHAR(20),
    IN p_search_term VARCHAR(100)
)
BEGIN
    SELECT 
        t.id,
        t.ticket_code,
        t.subject,
        t.location,
        t.urgency,
        t.status,
        t.created_at,
        t.updated_at,
        t.resolved_at,
        c.name AS category_name,
        c.icon AS category_icon,
        req.name AS requester_name,
        req.department AS requester_dept,
        tech.id AS technician_id,
        tech.name AS technician_name
    FROM `tickets` t
    INNER JOIN `categories` c ON t.category_id = c.id
    INNER JOIN `users` req ON t.user_id = req.id
    LEFT JOIN `users` tech ON t.assigned_to = tech.id
    WHERE (p_status_filter IS NULL OR p_status_filter = '' OR p_status_filter = 'all' OR t.status = p_status_filter)
      AND (p_category_filter IS NULL OR p_category_filter = '' OR p_category_filter = 'all' OR c.id = p_category_filter)
      AND (p_urgency_filter IS NULL OR p_urgency_filter = '' OR p_urgency_filter = 'all' OR t.urgency = p_urgency_filter)
      AND (p_search_term IS NULL OR p_search_term = '' 
           OR t.ticket_code LIKE CONCAT('%', p_search_term, '%')
           OR t.subject LIKE CONCAT('%', p_search_term, '%')
           OR t.location LIKE CONCAT('%', p_search_term, '%')
           OR req.name LIKE CONCAT('%', p_search_term, '%'))
    ORDER BY 
        CASE t.urgency
            WHEN 'Critical' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
        END ASC,
        t.created_at DESC;
END$$

-- 2. Assign ticket to a technician
CREATE PROCEDURE `sp_admin_assign_ticket`(
    IN p_ticket_id INT,
    IN p_admin_id INT,
    IN p_tech_id INT,
    IN p_note TEXT
)
BEGIN
    DECLARE v_tech_name VARCHAR(100);
    
    SELECT name INTO v_tech_name FROM `users` WHERE id = p_tech_id;
    
    UPDATE `tickets`
    SET `assigned_to` = p_tech_id,
        `status` = IF(status = 'Open', 'In Progress', status)
    WHERE `id` = p_ticket_id;
    
    INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`)
    VALUES (
        p_ticket_id, 
        p_admin_id, 
        'Assigned', 
        CONCAT('Ticket assigned to technician ', IFNULL(v_tech_name, 'Unassigned'), IF(p_note IS NOT NULL AND p_note != '', CONCAT('. Note: ', p_note), ''))
    );
END$$

-- 3. Comprehensive ticket update by admin
CREATE PROCEDURE `sp_admin_update_ticket`(
    IN p_ticket_id INT,
    IN p_admin_id INT,
    IN p_tech_id INT,
    IN p_status ENUM('Open', 'In Progress', 'Resolved', 'Closed'),
    IN p_urgency ENUM('Low', 'Medium', 'High', 'Critical'),
    IN p_resolution_note TEXT
)
BEGIN
    UPDATE `tickets`
    SET `assigned_to` = IF(p_tech_id > 0, p_tech_id, NULL),
        `status` = p_status,
        `urgency` = p_urgency,
        `resolution_summary` = IF(p_resolution_note IS NOT NULL AND p_resolution_note != '', p_resolution_note, `resolution_summary`),
        `resolved_at` = IF(p_status IN ('Resolved', 'Closed'), IFNULL(`resolved_at`, NOW()), NULL)
    WHERE `id` = p_ticket_id;
    
    INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`)
    VALUES (
        p_ticket_id, 
        p_admin_id, 
        'Status Updated', 
        CONCAT('Updated by Admin. Status: ', p_status, ', Urgency: ', p_urgency, IF(p_resolution_note IS NOT NULL AND p_resolution_note != '', CONCAT('. Note: ', p_resolution_note), ''))
    );
END$$

-- 4. Overview statistics for dashboard
CREATE PROCEDURE `sp_admin_get_dashboard_stats`()
BEGIN
    SELECT 
        COUNT(CASE WHEN urgency = 'Critical' AND status IN ('Open', 'In Progress') THEN 1 END) AS critical_count,
        COUNT(CASE WHEN status = 'Open' THEN 1 END) AS open_count,
        COUNT(CASE WHEN status = 'In Progress' THEN 1 END) AS in_progress_count,
        COUNT(CASE WHEN status = 'Resolved' AND DATE(resolved_at) = CURDATE() THEN 1 END) AS resolved_today_count,
        COUNT(CASE WHEN status = 'Resolved' THEN 1 END) AS total_resolved_count,
        COUNT(*) AS total_tickets_count
    FROM `tickets`;
END$$

-- 5. Incident History Log for audit & recurrence analysis
CREATE PROCEDURE `sp_admin_get_incident_history`(
    IN p_category_filter VARCHAR(50),
    IN p_search_term VARCHAR(100),
    IN p_limit INT
)
BEGIN
    SELECT 
        t.id,
        t.ticket_code,
        t.subject,
        t.location,
        t.urgency,
        t.status,
        t.created_at,
        t.resolved_at,
        TIMESTAMPDIFF(MINUTE, t.created_at, IFNULL(t.resolved_at, NOW())) AS duration_minutes,
        c.name AS category_name,
        req.name AS requester_name,
        req.department AS requester_dept,
        tech.name AS resolved_by_name,
        t.resolution_summary
    FROM `tickets` t
    INNER JOIN `categories` c ON t.category_id = c.id
    INNER JOIN `users` req ON t.user_id = req.id
    LEFT JOIN `users` tech ON t.assigned_to = tech.id
    WHERE t.status IN ('Resolved', 'Closed')
      AND (p_category_filter IS NULL OR p_category_filter = '' OR p_category_filter = 'all' OR c.id = p_category_filter)
      AND (p_search_term IS NULL OR p_search_term = '' 
           OR t.ticket_code LIKE CONCAT('%', p_search_term, '%')
           OR t.subject LIKE CONCAT('%', p_search_term, '%')
           OR t.location LIKE CONCAT('%', p_search_term, '%')
           OR req.name LIKE CONCAT('%', p_search_term, '%'))
    ORDER BY t.resolved_at DESC
    LIMIT p_limit;
END$$

DELIMITER ;
