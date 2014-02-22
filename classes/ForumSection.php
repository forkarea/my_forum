<?php
	include_once "./classes/ForumThread.php";
        include_once "./database_connection.php";
        include_once "./classes/User.php";

	class ForumSection {
		private $dbh;
		public function __construct ($dbh) {
			$this->dbh = $dbh;
		}
	
		public function add_thread($name, $user) {
		        $stmt = $this->dbh->prepare('insert into threads (name, time, created_by_user) values (:text, :time, :user_id)');
		        $stmt->bindParam(':text', $name);
                        $time = current_date_for_db();
			$stmt->bindParam(':time', $time);
			$stmt->bindParam(':user_id', $user->user_id);
		        if($stmt->execute()) {
			        $new_thread_id = $this->dbh->lastInsertId();
			        return ForumThread::construct($this->dbh, $new_thread_id, $name, $time, $user->user_id);
		        } else {
		        	return NULL;
		        }
		}
		
                public function get_thread($thread_id) {
		        $stmt = $this->dbh->prepare('SELECT * FROM threads WHERE id=:thread_id');
			$stmt->bindParam(':thread_id', $thread_id);
                        
			if ($stmt->execute()) {
                                $thread = $stmt->fetchObject("ForumThread", array($this->dbh));
				return $thread;
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
