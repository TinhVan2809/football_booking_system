<?php
    require_once '../connection.php';
    
    class Branches {
        //# Lấy danh sách branches để làm tìm kiếm 
        public function searchBranches() {
            try{
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $sql = "SELECT branch_id, branch_name FROM branches";
            $stmt = $connection->prepare($sql);
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch(PDOException $e) {
                error_log("Error searching branches " . $e->getMessage());
                return [];
            }
        }

        //# Lấy danh sách field khi đã có branch_id (các sân bóng mà chi nhánh đó có)

        public function searchFieldTypesByBranch(int $branch_id) {
            try{
                $db = Database::getInstance();
                $connection = $db->getConnection();
                $sql = "SELECT f.field_id, f.field_name FROM fields WHERE branch_id = :branch_id";
                $stmt = $connection->prepare($sql);
                $stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch(PDOException $e) {
                error_log("Error searching field types " . $e->getMessage());
                return [];
            }
        }
    } 

?>