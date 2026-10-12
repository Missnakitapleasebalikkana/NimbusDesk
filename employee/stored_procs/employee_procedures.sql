-- ===================================================
-- NimbusDesk: Employee Stored Procedures
-- Purpose: Handlers specifically for Airline Staff / Ticket Creators
-- ===================================================

USE `nimbusdesk`;

DROP PROCEDURE IF EXISTS `sp_employee_create_ticket`;
DROP PROCEDURE IF EXISTS `sp_employee_get_tickets`;
DROP PROCEDURE IF EXISTS `sp_employee_get_ticket_details`;
DROP PROCEDURE IF EXISTS `sp_employee_get_stats`;

DELIMITER $$

-- 1. Create a new support ticket
CREATE PROCEDURE `sp_employee_create_ticket`(
    IN p_user_id INT,
    IN p_category_id INT,
    IN p_urgency ENUM('Low', 'Medium', 'High', 'Critical'),
    IN p_location VARCHAR(150),
    IN p_subject VARCHAR(255),
    IN p_description TEXT,
    IN p_attachment VARCHAR(255),
    OUT p_new_id INT,
    OUT p_ticket_code VARCHAR(20)
)
BEGIN
    DECLARE next_num INT;
    
    -- Generate unique ticket code
    SELECT IFNULL(MAX(id), 10000) + 1 INTO next_num FROM `tickets`;
    SET p_ticket_code = CONCAT('NB-', next_num);
    
    -- Insert ticket
    INSERT INTO `tickets` (
        `ticket_code`, `user_id`, `category_id`, `urgency`, 
        `location`, `subject`, `description`, `attachment`, `status`
    ) VALUES (
        p_ticket_code, p_user_id, p_category_id, p_urgency, 
        p_location, p_subject, p_description, p_attachment, 'Open'
    );
    
    SET p_new_id = LAST_INSERT_ID();
    
    -- Insert creation event into logs
    INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`)
    VALUES (p_new_id, p_user_id, 'Created', CONCAT('Ticket submitted with urgency: ', p_urgency));
END$$

-- 2. Retrieve tickets created by employee
CREATE PROCEDURE `sp_employee_get_tickets`(
    IN p_user_id INT,
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
        t.resolved_at,
        c.name AS category_name,
        c.icon AS category_icon,
        tech.name AS technician_name
    FROM `tickets` t
    INNER JOIN `categories` c ON t.category_id = c.id
    LEFT JOIN `users` tech ON t.assigned_to = tech.id
    WHERE t.user_id = p_user_id
      AND (p_status_filter IS NULL OR p_status_filter = '' OR p_status_filter = 'all' OR t.status = p_status_filter)
    ORDER BY t.created_at DESC;
END$$

-- 3. Retrieve single ticket detail for employee
CREATE PROCEDURE `sp_employee_get_ticket_details`(
    IN p_ticket_id INT,
    IN p_user_id INT
)
BEGIN
    -- Ticket metadata
    SELECT 
        t.*,
        c.name AS category_name,
        c.icon AS category_icon,
        u.name AS requester_name,
        u.department AS requester_dept,
        tech.name AS technician_name
    FROM `tickets` t
    INNER JOIN `categories` c ON t.category_id = c.id
    INNER JOIN `users` u ON t.user_id = u.id
    LEFT JOIN `users` tech ON t.assigned_to = tech.id
    WHERE t.id = p_ticket_id AND t.user_id = p_user_id;
    
    -- Resolution updates log
    SELECT 
        l.id,
        l.action_type,
        l.notes,
        l.created_at,
        u.name AS actor_name,
        u.role AS actor_role
    FROM `ticket_logs` l
    INNER JOIN `users` u ON l.user_id = u.id
    WHERE l.ticket_id = p_ticket_id
    ORDER BY l.created_at ASC;
END$$

-- 4. Get ticket counts summary for employee
CREATE PROCEDURE `sp_employee_get_stats`(
    IN p_user_id INT
)
BEGIN
    SELECT 
        COUNT(CASE WHEN status IN ('Open', 'In Progress') THEN 1 END) AS active_count,
        COUNT(CASE WHEN status = 'Resolved' THEN 1 END) AS resolved_count,
        COUNT(*) AS total_count
    FROM `tickets`
    WHERE user_id = p_user_id;
END$$

DELIMITER ;
