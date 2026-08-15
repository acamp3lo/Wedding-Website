USE wedding_db;

CREATE TABLE gifts (
    id INT UNSIGNED PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    total_value INT UNSIGNED NOT NULL,
    contributed_value INT UNSIGNED NOT NULL,
    CHECK (total_value >= 0),
    CHECK (contributed_value <= total_value)
);

CREATE TABLE guest_confirmations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100) NOT NULL,
    food_restrictions VARCHAR(300) NULL,
    is_attending BOOLEAN NOT NULL
);

CREATE TABLE guest_gifts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(250) NOT NULL,
    gift_id INT UNSIGNED NULL,
    gift_value INT UNSIGNED NOT NULL,
    message VARCHAR(600) NULL,
    FOREIGN KEY (gift_id) REFERENCES gifts(id) ON DELETE SET NULL
);


DELIMITER //

CREATE TRIGGER update_gift_after_contribution
AFTER INSERT ON guest_gifts
FOR EACH ROW
BEGIN
    -- Only update if there is actually a gift_id attached
    IF NEW.gift_id IS NOT NULL THEN
        UPDATE gifts
        SET contributed_value = contributed_value + NEW.gift_value
        WHERE id = NEW.gift_id;
    END IF;
END; //

CREATE TRIGGER update_gift_after_delete
AFTER DELETE ON guest_gifts
FOR EACH ROW
BEGIN
    IF OLD.gift_id IS NOT NULL THEN
        UPDATE gifts
        SET contributed_value = contributed_value - OLD.gift_value
        WHERE id = OLD.gift_id;
    END IF;
END; //

DELIMITER ;
