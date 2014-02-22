<?php 
	class ForumThread {
                private $dbh = NULL;
                private $error_msg = NULL;
                
                public $id = NULL;
                public $name = NULL;
                public $time = NULL;
                public $created_by_user = NULL;

                public function __construct($dbh) {
                        $this->dbh = $dbh;
                }

		public static function construct($dbh, $id, $name, $time, $created_by_user) {
                        $thr = new ForumThread($dbh);
                        $thr->dbh = $dbh;

			$thr->id = $id;
                        $thr->name = $name;
                        $thr->time = $time;
                        $thr->created_by_user = $created_by_user;
                        return $thr;
		}
		
		public function get_id() {
			return $this->id;
		}
		
		public function get_name() {
                        return $this->name;
		}
		
		public function add_post($text ) {
                        //we really need string length in bytes
                        //because the array column is of type varbinary
                        $text_length = strlen($text);
                        if ($text_length === 0) {
                                $this->error_msg = "The message cannot be empty!";
                                return false;
                        } else if ($text_length > 9990) {
                                $this->error_msg = "The message is too long!";
                                return false;
                        } 
                        $match_result = preg_match('|^[[:space:]]*$|', $text);
                        if ($match_result !== 0) {
                                $this->error_msg = "Message cannot contain only whitespace!";
                                return false;
                        }

			$stmt = $this->dbh->prepare('insert into posts (text, thread_id, time) values (:text, :thread_id, :time)');
			$stmt->bindParam(':text', $text);
			$stmt->bindParam(':thread_id', $this->id);
			$stmt->bindParam(':time', date('Y-m-d G:i:s'));
			if(! $stmt->execute()) {
				return false;
			} else {
				return true;
			}
		}
		
		public function get_all_posts() {
                        try {
                                $stmt = $this->dbh->prepare('SELECT text, time FROM posts WHERE thread_id=:thread_id');
                                $stmt->bindParam(':thread_id', $this->id);
                                if ($stmt->execute()) {
                                        return $stmt;
                                } else {
                                        return NULL;
                                }
                        } catch (Exception $e) {
                                $this->error_msg = $e->getMessage();
                        }
                }

                //The error message should be save to display in HTML
                public function get_last_error() {
                        return $this->error_msg;
                }

                public function get_user_creator() {
                        $um = new UserManager($this->dbh);
                        return $um->get_user_by_id($this->created_by_user);
                }
        };
