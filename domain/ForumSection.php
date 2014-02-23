<?php
        namespace domain;
        use PDO;
        include_once "./database_connection.php";

	class ForumSection {
		private $dbh;
		public function __construct ($dbh) {
			$this->dbh = $dbh;
		}
	
		public function add_thread($name, $user) {
                        $thread = ForumThread::create_as_new($this->dbh, $name, $user->user_id, $this->error_msg);
                        return $thread->persist();
		}
		
                public function get_thread($thread_id) {
		        $stmt = $this->dbh->prepare('SELECT * FROM threads WHERE id=:thread_id');
			$stmt->bindParam(':thread_id', $thread_id);
                        
			if ($stmt->execute()) {
                                $thread = $stmt->fetchObject("domain\\ForumThread", array($this->dbh));
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
