<?php
        namespace domain;
        use PDO;

	class ForumThread
	{
                private $dbh = NULL;
                private $get_posts_stmt = NULL;

                public $id = NULL;
                public $name = NULL;
                public $time = NULL;
                public $created_by_user = NULL;

                public function __construct($dbh)
                {
                        $this->dbh = $dbh;
                }

                //create a new thread that is to be stored into the database
                //__construct is used for PDO for getting the database row as a class
                public static function create_as_new($dbh, $name, $user, &$error_msg)
                {
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

                //store in the database
                public function persist(&$error_msg)
                {
		        $stmt = $this->dbh->prepare('insert into threads (name, time, created_by_user) values (:text, :time, :user_id)');
		        $stmt->bindParam(':text', $this->name);
			$stmt->bindParam(':time', $this->time);
			$stmt->bindParam(':user_id', $this->created_by_user);
		        if ($stmt->execute()) {
			        $this->id = $this->dbh->lastInsertId();
                                return true;
		        } else {
		        	return false;
		        }
                }

		public function get_id()
		{
			return $this->id;
		}

		public function get_name()
		{
                        return $this->name;
		}

                public function add_post($post, &$error_msg)
                {
                        $post->thread_id = $this->id;

                        return $post->persist($error_msg);
                }

                public function initiate_getting_all_posts(&$error_msg)
                {
                        try {
                                $stmt = $this->dbh->prepare('SELECT * FROM posts WHERE thread_id=:thread_id');
                                $stmt->bindParam(':thread_id', $this->id);
                                if ($stmt->execute()) {
                                        $this->get_posts_stmt = $stmt;
                                        return true;
                                } else {
                                        return false;
                                }
                        } catch (Exception $e) {
                                $error_msg = 'Cannot get posts from the database';
                                return false;
                        }

                }

                public function get_next_post()
                {
                        if ($this->get_posts_stmt === NULL){
                                return NULL;
                        }

                        try {
                                $post = $this->get_posts_stmt->fetchObject('\domain\ForumPost', array($this->dbh));
                                if (!is_object($post)) {
                                        $this->get_posts_stmt = NULL;
                                        return null;
                                }
                                return $post;
                        } catch (\PDOException $ex) {
                                $this->get_posts_stmt = NULL;
                                return null;
                        }
                }

                public function get_user_creator($um)
                {
                        if (!is_object($um)) {
                                $um = new UserManager($this->dbh);
                        }

                        return $um->get_user_by_id($this->created_by_user);
                }
        };
