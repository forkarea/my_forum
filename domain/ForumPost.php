<?php
        namespace domain;
        use PDO;

        class ForumPost
        {
                public $dbh;

                public $post_id;
                public $text;
                public $thread_id;
                public $creation_time;
                public $created_by_user;

                public function __construct($dbh)
                {
                        $this->dbh = $dbh;
                }

                public static function construct($dbh, $post_id, $text, $thread_id, $creation_time, $created_by_user)
                {
                        $r = new ForumPost($dbh);

                        $r->post_id = $post_id;
                        $r->text = $text;
                        $r->thread_id = $thread_id;
                        $r->creation_time = $creation_time;
                        $r->created_by_user = $created_by_user;

                        return $r;
                }


                public static function create_as_new($dbh, $text, $user, &$error_msg)
                {
                        //we really need string length in bytes
                        //because the array column is of type varbinary
                        $text_length = strlen($text);
                        if ($text_length === 0) {
                                $error_msg = "The message cannot be empty!";

                                return null;
                        } elseif ($text_length > 9990) {
                                $error_msg = "The message is too long!";

                                return null;
                        }
                        $match_result = preg_match('|^[[:space:]]*$|', $text);
                        if ($match_result !== 0) {
                                $error_msg = "Message cannot contain only whitespace!";

                                return null;
                        }

                        $r = new ForumPost($dbh);

                        $r->post_id = NULL;
                        $r->text = $text;
                        $r->thread_id = NULL;
                        $r->creation_time = \utility\DatabaseConnection::getCurrentDateForDb();
                        if ($user === NULL) {
                                $r->created_by_user = NULL;
                        } else {
                                $r->created_by_user = $user->user_id;
                        }

                        return $r;
                }


                public function persist(&$error_msg)
                {
                        try {
                                $stmt = $this->dbh->prepare('insert into posts (text, thread_id, creation_time, created_by_user) values (:text, :thread_id, :creation_time, :created_by_user)');
                                $stmt->bindValue(':text', $this->text);
                                $stmt->bindValue(':thread_id', $this->thread_id);
                                $stmt->bindValue(':creation_time', $this->creation_time);
                                $stmt->bindValue(':created_by_user', $this->created_by_user);
                                if(! $stmt->execute()) {
                                        return false;
                                } else {
                                        $this->post_id = $this->dbh->lastInsertId();

                                        return true;
                                }
                        } catch (PDOException $ex) {
                                trigger_warning('PDO Error: ' . $ex->getMessage());
                                $error_msg = "Cannot save the value to database";

                                return false;
                        }
                }
        };

