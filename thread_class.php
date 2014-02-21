<?php 
	class ForumThread {
		private $thread_id = NULL;
                private $error_msg = NULL;

		public function __construct($thread_id_in) {
			$this->thread_id = $thread_id_in;
			//echo 'creating thread';
			//echo $thread_id;
		}
		
		public function get_id() {
			return $this->thread_id;
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

			$stmt = $dbh->prepare('insert into posts (text, thread_id, time) values (:text, :thread_id, :time)');
			$stmt->bindParam(':text', $text);
			$stmt->bindParam(':thread_id', $this->thread_id);
			$stmt->bindParam(':time', date('Y-m-d G:i:s'));
			if(! $stmt->execute()) {
				return false;
			} else {
				return true;
			}
		}
		
		public function get_all_posts( $dbh ) {
                        try {
                                $stmt = $dbh->prepare('SELECT text, time FROM posts WHERE thread_id=:thread_id');
                                $stmt->bindParam(':thread_id', $this->thread_id);
                                if ($stmt->execute())
                                        return $stmt;
                                else
                                        return NULL;
                        } catch (Exception $e) {
                                $this->error_msg = $e->getMessage();
                        }
                }

                //The error message should be save to display in HTML
                public function get_last_error() {
                        return $this->error_msg;
                }
        };
