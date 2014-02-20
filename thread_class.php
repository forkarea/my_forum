<?php 
	class ForumThread {
		private $thread_id = NULL;
		public function __construct($thread_id_in) {
			$this->thread_id = $thread_id_in;
			//echo 'creating thread';
			//echo $thread_id;
		}
		
		public function get_id() {
			return $this->id;
		}
		
		public function get_name( $dbh ) {
		        $stmt = $dbh->prepare('SELECT name FROM threads WHERE id=:thread_id');
			$stmt->bindParam(':thread_id', $this->thread_id);
			if ($stmt->execute()) {
				$row = $stmt->fetch();
				$thread_name = $row[0];
				return $thread_name;
			} else {
				return NULL;
			}
		}
		
		public function add_post( $dbh, $text ) {
			$stmt = $dbh->prepare('insert into posts (text, thread_id) values (:text, :thread_id)');
			$stmt->bindParam(':text', $text);
			$stmt->bindParam(':thread_id', $this->thread_id);
			if(! $stmt->execute()) {
				return false;
			} else {
				return true;
			}
		}
		
		public function get_all_posts( $dbh ) {
		        $stmt = $dbh->prepare('SELECT text FROM posts WHERE thread_id=:thread_id');
			$stmt->bindParam(':thread_id', $this->thread_id);
			if ($stmt->execute())
				return $stmt;
			else
				return NULL;
                }
        };
