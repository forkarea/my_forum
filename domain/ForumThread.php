<?php 
        namespace domain;
        use PDO;

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

                public static function create_as_new($dbh, $name, $user, &$error_msg) {
                        $name_length = strlen($name);
                        if ($name_length === 0) {
                                $error_msg = "Please write the thread name!";
                                return null;
                        } elseif ($name_length > 950) {
                                $error_msg = "The thread name is too long!";
                                return null;
                        } 

                        $thr = new ForumThread($dbh);
                        $thr->dbh = $dbh;

			$thr->id = null;
                        $thr->name = $name;
                        $thr->time = \utility\DatabaseConnection::getCurrentDateForDb();
                        $thr->created_by_user = $user->user_id;
                        return $thr;
                }

                public function persist(&$error_msg) {
		        $stmt = $this->dbh->prepare('insert into threads (name, time, created_by_user) values (:text, :time, :user_id)');
		        $stmt->bindParam(':text', $this->name);
			$stmt->bindParam(':time', $this->time);
			$stmt->bindParam(':user_id', $this->created_by_user);
		        if($stmt->execute()) {
			        $this->id = $this->dbh->lastInsertId();
                                return true;
		        } else {
		        	return false;
		        }
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
                        $post = ForumPost::create_as_new($this->dbh, $text, NULL, $this->error_msg);
                        if ($post === NULL)
                                return false;
                        $post->thread_id = $this->id;
                        return $post->persist($this->error_msg);
		}

                public function add_post_raw($post) {
                        $post->thread_id = $this->id;
                        return $post->persist($this->error_msg);
                }
		
		public function get_all_posts() {
                        try {
                                $stmt = $this->dbh->prepare('SELECT text, creation_time FROM posts WHERE thread_id=:thread_id');
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
