<?php

require_once '../connection.php';

class Branch
{
    public function getBranchById(int $branch_id)
    {
        if (empty($branch_id)) {
            return false;
        }
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $sql = "SELECT
                        f.field_id,
                        f.field_name,
                        f.status,

                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'field_type_id', ft.field_type_id,
                                'type_name', ft.type_name,
                                'players', ft.players,
                                'price_per_hour', fft.price_per_hour
                            )
                        ) AS field_types
                    FROM fields f
                    JOIN field_field_types fft ON fft.field_id = f.field_id
                    JOIN field_types ft ON ft.field_type_id = fft.field_type_id
                    WHERE f.branch_id = :branch_id
                    GROUP BY f.field_id, f.field_name, f.status 
                    LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error getting branches " . $e->getMessage());
            return [];
        }
    }

    public function getBranches(int $limit = 20, $offset = 0) {
        try{
            $db = Database::getInstance();
            $connection = $db->getConnection();
             $sql = "SELECT branch_id, branch_name, address, phone, open_time, close_time FROM branches LIMIT :limit OFFSET :offset";
             $stmt = $connection->prepare($sql);
             $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
             $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
             $stmt->execute();

             return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Erro getting branches " . $e->getMessage());
            return [];
        }
    }

    public function countBranches()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();

            $sql = "SELECT COUNT(branch_id) FROM branches";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return isset($row['total']) ? (int)$row['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error counting branches " . $e->getMessage());
            return [];
        }
    }

    public function addBranch($branch_name, $address, $phone, $open_time, $close_time)  {
        try{
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $sql = "INSERT INTO branches (branch_name, address, phone, open_time, close_time)
                    VALUES (:branch_name, :address, :phone, :open_time, :close_time)";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(":branch_name", trim($branch_name), PDO::PARAM_STR);
            $stmt->bindValue(":address", trim($address), PDO::PARAM_STR);
            $stmt->bindValue(":phone", trim($phone), PDO::PARAM_STR);
            $stmt->bindValue(":open_time", trim($open_time), PDO::PARAM_STR);
            $stmt->bindValue(":close_time", trim($close_time), PDO::PARAM_STR);
            $stmt->execute();
            return $connection->lastInsertId();


        } catch(PdoException $e) {
            error_log("Error adding branch " . $e->getMessage());
            return false;

        }
    }

    public function updateBranch($branch_id, $branch_name, $address, $phone, $open_time, $close_time) {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $sql = "UPDATE branches
                    SET branch_name = :branch_name,
                        address = :address,
                        phone = :phone,
                        open_time = :open_time,
                        close_time = :close_time
                    WHERE branch_id = :branch_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(":branch_name", trim($branch_name), PDO::PARAM_STR);
            $stmt->bindValue(":address", trim($address), PDO::PARAM_STR);
            $stmt->bindValue(":phone", trim($phone), PDO::PARAM_STR);
            $stmt->bindValue(":open_time", trim($open_time), PDO::PARAM_STR);
            $stmt->bindValue(":close_time", trim($close_time), PDO::PARAM_STR);
            $stmt->bindValue(":branch_id", $branch_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating branch " . $e->getMessage());
            return false;
        }
    }
}
