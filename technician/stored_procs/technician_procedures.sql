-- ===================================================
-- NimbusDesk: Technician Stored Procedures
-- Purpose: Handlers specifically for IT Help Desk Technicians
-- ===================================================

USE `nimbusdesk`;

DROP PROCEDURE IF EXISTS `sp_tech_get_assigned_tickets`;
DROP PROCEDURE IF EXISTS `sp_tech_update_ticket_status`;
DROP PROCEDURE IF EXISTS `sp_tech_add_resolution_note`;
DROP PROCEDURE IF EXISTS `sp_tech_get_stats`;

DELIMITER $$

-- 1. Retrieve queue of tickets for technician
CREATE PROCEDURE `sp_tech_get_assigned_tickets`(
    IN p_tech_id INT,
    IN p_status_filter VARCHAR(20)
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
        c.name AS category_name,
        c.icon AS category_icon,
        u.name AS requester_name,
        u.department AS requester_dept,
        tech.name AS technician_name
    FROM `tickets` t
    INNER JOIN `categories` c ON t.category_id = c.id
    INNER JOIN `users` u ON t.user_id = u.id
    LEFT JOIN `users` tech ON t.assigned_to = tech.id
    WHERE (t.assigned_to = p_tech_id OR (t.assigned_to IS NULL AND t.status = 'Open'))
      AND (p_status_filter IS NULL OR p_status_filter = '' OR p_status_filter = 'all' OR t.status = p_status_filter)
    ORDER BY 
        CASE t.urgency
            WHEN 'Critical' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
        END ASC,
        t.created_at ASC;
END$$

-- 2. Update status of ticket and log action
CREATE PROCEDURE `sp_tech_update_ticket_status`(
    IN p_ticket_id INT,
    IN p_tech_id INT,
    IN p_new_status ENUM('Open', 'In Progress', 'Resolved', 'Closed'),
    IN p_resolution_summary TEXT,
    IN p_note TEXT
)
BEGIN
    -- Update ticket
    IF p_new_status = 'Resolved' THEN
        UPDATE `tickets`
        SET `status` = p_new_status,
            `assigned_to` = IFNULL(`assigned_to`, p_tech_id),
            `resolution_summary` = IFNULL(p_resolution_summary, `resolution_summary`),
            `resolved_at` = NOW()
        WHERE `id` = p_ticket_id;
    ELSE
        UPDATE `tickets`
        SET `status` = p_new_status,
            `assigned_to` = IFNULL(`assigned_to`, p_tech_id)
        WHERE `id` = p_ticket_id;
    END IF;

    -- Add activity entry
    INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`)
    VALUES (
        p_ticket_id, 
        p_tech_id, 
        'Status Updated', 
        CONCAT('Status changed to ', p_new_status, IF(p_note IS NOT NULL AND p_note != '', CONCAT('. Note: ', p_note), ''))
    );
END$$

-- 3. Add troubleshooting update / note to ticket
CREATE PROCEDURE `sp_tech_add_resolution_note`(
    IN p_ticket_id INT,
    IN p_tech_id INT,
    IN p_note TEXT
)
BEGIN
    INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`)
    VALUES (p_ticket_id, p_tech_id, 'Note Added', p_note);
END$$

-- 4. Get technician workload stats
CREATE PROCEDURE `sp_tech_get_stats`(
    IN p_tech_id INT
)
BEGIN
    SELECT 
        COUNT(CASE WHEN assigned_to = p_tech_id AND status = 'In Progress' THEN 1 END) AS in_progress_count,
        COUNT(CASE WHEN assigned_to IS NULL AND status = 'Open' THEN 1 END) AS unassigned_open_count,
        COUNT(CASE WHEN assigned_to = p_tech_id AND status = 'Resolved' AND DATE(resolved_at) = CURDATE() THEN 1 END) AS resolved_today_count,
        COUNT(CASE WHEN assigned_to = p_tech_id AND status = 'Resolved' THEN 1 END) AS total_resolved_count
    FROM `tickets`;
END$$

DELIMITER ;
