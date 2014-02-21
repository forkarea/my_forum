<?php
	include_once "./classes/ForumThread.php";
        include_once "./database_connection.php";

	class ForumSection {
		private $dbh;
		public function __construct ($dbh) {
			$this->dbh = $dbh;
		}
	
		public function add_thread($name) {
		        $stmt = $this->dbh->prepare('insert into threads (name, time) values (:text, :time)');
		        $stmt->bindParam(':text', $name);
			$stmt->bindParam(':time', current_date_for_db());
		        if($stmt->execute()) {
			        $new_thread_id = $this->dbh->lastInsertId();
			        return new ForumThread($new_thread_id);
		        } else {
		        	return NULL;
		        }
		}
		
		public function get_all_threads() {
		        $stmt = $this->dbh->prepare('SELECT id, name, time from threads');
        		if ($stmt->execute()) { 
        			return $stmt;
        		} else {
        			return NULL;
        		}	
		}
	
	
	
	
	
	};
