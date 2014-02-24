<?php
        namespace domain;
        use PDO;

	class ForumSection
	{
		private $dbh;
                private $get_threads_stmt;
		public function __construct($dbh)
		{
			$this->dbh = $dbh;
		}

		public function add_thread($name, $user)
		{
                        $thread = ForumThread::create_as_new($this->dbh, $name, $user->user_id, $this->error_msg);

                        return $thread->persist();
		}

                public function get_thread($thread_id)
                {
		        $stmt = $this->dbh->prepare('SELECT * FROM threads WHERE id=:thread_id');
			$stmt->bindParam(':thread_id', $thread_id);

			if ($stmt->execute()) {
                                $thread = $stmt->fetchObject("domain\\ForumThread", array($this->dbh));
				return $thread;
			} else {
				return NULL;
			}
                }

		public function init_get_all_threads()
		{
                        try {
                                $stmt = $this->dbh->prepare('SELECT * from threads');

                                if ($stmt->execute()) {
                                        $this->get_threads_stmt = $stmt;
                                        return true;
                                } else {
                                        return false;
                                }
                        } catch (\PDOException $ex) {
                                return false;
                        }
		}
                public function get_next_thread()
                {
                        try {
                                $thread = $this->get_threads_stmt->fetchObject("domain\\ForumThread", array($this->dbh));
                                if (!is_object($thread)) {
                                        return null;
                                }
                                return $thread;
                        } catch (\PDOException $ex) {
                                return null;
                        }
                }
	};
