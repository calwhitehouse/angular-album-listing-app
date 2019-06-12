<?php

require_once('./classes/ApplicationRegistry.class.php');
require_once('setEnv.php');
require_once('./classes/JSON_RecordSet.php');
require_once('./classes/session.class.php');

$action  = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null;
$id      = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
//Update student details - code to retrieve the data we send using php's file_get_contents('php://input')
if (empty($action)) {
    if ((($_SERVER['REQUEST_METHOD'] == 'POST') ||
            ($_SERVER['REQUEST_METHOD'] == 'PUT') ||
            ($_SERVER['REQUEST_METHOD'] == 'DELETE')) &&
        (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
    ) {

        $input = json_decode(file_get_contents('php://input'), true);

        $action  = isset($input['action']) ? $input['action'] : null;
        $subject = isset($input['subject']) ? $input['subject'] : null;
        $data    = isset($input['data']) ? $input['data'] : null;
    }
}

// concat action and subject with uppercase first letter of subject
$route = $action . ucfirst($subject); // eg list course becomes listCourse

$db = ApplicationRegistry::DB(); // connect to db

//set the header to json because everything is returned in that format
header("Content-Type: application/json");

// take the appropriate action based on the action and subject
switch ($route) {
    case 'listAlbums':

        $sqlAlbums = "	SELECT DISTINCT a.name AS genre, b.name AS album, b.artwork, b.year, SUM(d.total_time) AS total_time, e.name AS artistname, b.album_id
                        FROM i_artist e
                        JOIN i_track d ON d.artist_id = e.artist_id
                        JOIN i_album_track c ON c.track_id = d.track_id
                        LEFT JOIN i_album b ON b.album_id = c.album_id
                        LEFT JOIN i_genre a ON a.genre_id = b.genre_id
                        GROUP BY album ORDER BY album";

        $rs     = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlAlbums);
        echo $retval;
        break;
    case 'listTracks':
	    $id        = $db->quote($id);
        $sqlTracks = "  SELECT a.name AS artistname, b.name AS trackname, c.track_number, b.total_time, d.album_id, d.artwork, b.size
						FROM i_artist a
						JOIN i_track b ON a.artist_id = b.artist_id
						JOIN i_album_track c ON b.track_id = c.track_id
						JOIN i_album d ON c.album_id = d.album_id
						WHERE d.album_id = $id
						ORDER BY c.track_number";

        $rs     = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlTracks, 'ResultSet');
        echo $retval;
        break;
	case 'listGenre':
	        $sqlGenre = "	SELECT DISTINCT name AS genre, genre_id
	                      	FROM i_genre
	                        ORDER BY genre";

	        $rs     = new JSONRecordSet();
	        $retval = $rs->getRecordSet($sqlGenre);
	        echo $retval;
	        break;
    case 'searchGenre':
        $id        = $db->quote($id);
        $sqlAlbums = "SELECT DISTINCT a.name AS genre, a.genre_id as genreid, b.name AS album, b.artwork, b.year, SUM(d.total_time) AS total_time, e.name AS artistname, b.album_id
                      	FROM i_genre a
                        JOIN i_album b ON a.genre_id = b.genre_id
                        JOIN i_album_track c ON b.album_id = c.album_id
                        JOIN i_track d ON c.track_id = d.track_id
                        JOIN i_artist e ON d.artist_id = e.artist_id
                        WHERE genreid= $id
                        GROUP BY album ORDER BY album";

        $rs     = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlAlbums);
        echo $retval;
        break;
    case 'searchAlbums':
        $data      = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
        //sanitize data
        $data = filter_var($data, FILTER_SANITIZE_STRING & FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH |  FILTER_FLAG_STRIP_LOW);
        //concat wildcard characters
        $data = '%' . $data . '%';

            $sqlSearch = "SELECT DISTINCT a.name AS genre, b.name AS album, b.artwork, b.year, SUM(d.total_time) AS total_time, e.name AS artistname, b.album_id
                        FROM i_artist e
                        JOIN i_track d ON d.artist_id = e.artist_id
                        JOIN i_album_track c ON c.track_id = d.track_id
                        LEFT JOIN i_album b ON b.album_id = c.album_id
                        LEFT JOIN i_genre a ON a.genre_id = b.genre_id
			            WHERE (album LIKE :term OR artistname LIKE :term)
                        GROUP BY album ORDER BY album";
        $params = array(':term' => $data);
            $rs = new JSONRecordSet();
            $retval = $rs->getRecordSet($sqlSearch, 'ResultSet', $params);
            echo $retval;
            break;
    case 'showNotes':
        $id = $db->quote($id);
        $session = Session::getInstance();
        if ($session->getProperty('username') !== ""){
            $userid = $session->getProperty('username');
            $sqlNotes = "SELECT * FROM i_notes WHERE album_id = $id and userID = '$userid'";
            $rs = new JSONRecordSet();
            $retval = $rs->getRecordSet($sqlNotes, 'ResultSet');
            echo $retval;
        } else {
           echo '{"status":"error", "message":{"text": "Please login to see notes"}}';
        }

        break;
    case 'updateNotes':
        //Wrap testing.php note form data into json object
        if (!isset($data) && $_REQUEST['notes']) {
            $data['album_id'] = $_REQUEST['album_id'];
            $data['notes'] = $_REQUEST['notes'];
            $data = json_encode($data);
        }
         //use data to update notes
        if (!empty($data)) {
            $data          = json_decode($data);
            $notes = $data->notes;
            //sanitize data
            $notes = filter_var($notes, FILTER_SANITIZE_STRING & FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH |  FILTER_FLAG_STRIP_LOW);
            $session = Session::getInstance();
            if ($session->getProperty('username') !== "") {
                $userid = $session->getProperty('username');
                $updateNotesSQL = "UPDATE i_notes SET note=:note WHERE  album_id=:album_id AND userID=:userID";
                $rs               = new JSONRecordSet();
                $retval           = $rs->getRecordSet($updateNotesSQL,
                    'ResultSet',
                    array(
                        ':note'   => $data->notes,
                        ':album_id'  => $data->album_id,
                        ':userID'     => $userid
                    ));
                echo $notes;
            } else {
                echo '{"status":"error", "message":{"text": "Please login to add a note"}}';
            }
        }
        break;
    case 'addNote';
        //Wrap testing.php note form data into json object
        if (!isset($data) && $_REQUEST['notes']){
            $data['album_id']= $_REQUEST['album_id'];
            $data['notes']= $_REQUEST['notes'];
            $data = json_encode($data);
        }
        if (!empty($data)) {
            $data = json_decode($data);
            $notes = $data->notes;
            //sanitize data
            $notes = filter_var($notes, FILTER_SANITIZE_STRING & FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH |  FILTER_FLAG_STRIP_LOW);
            $session = Session::getInstance();
            if ($session->getProperty('username') !== "") {
                $userid = $session->getProperty('username');
                //SQL stmt with placeholder values
                $sqlNotes = "INSERT INTO i_notes (album_id, userID, note) VALUES (:album_id, :userID, :note)";
                $rs = new JSONRecordSet();
                $retval = $rs->getRecordSet($sqlNotes,
                    'ResultSet',
                    array(
                        ':album_id' => $data->album_id,
                        ':userID' => $userid,
                        ':note' => $notes
                    ));
                echo $notes;
            } else {
                echo '{"status":"error", "message":{"text": "Please login to add a note"}}';
            }
        }
    break;
    /**
     * login the user given a username and password, though only via $_POST
     */
    case 'loginUser':
        /**
         * on the following two lines where I have $yourcode you will need to
         * replace it with code to retrieve from post
         * remember if angular this isn't in $_POST;  though with FlexBuilder it probably is
         */

        //Wrap testing.php login form data into json object
        if (!isset($data) && $_REQUEST['username']){
            $data['username']= $_REQUEST['username'];
            $data['password']= $_REQUEST['password'];
            $data = json_encode($data);

        }
        $user    = json_decode($data);
        $usr     = $user->username;
        $passw   = $user->password;
        //trim whitespace
        $usr = trim($usr);
        $passw = trim($passw);
        //sanitize data
        $usr = filter_var($usr, FILTER_SANITIZE_EMAIL);
        $passw = filter_var($passw, FILTER_SANITIZE_STRING);
        $session = Session::getInstance();
        if (!empty($usr) && !empty($passw)) { // if both username and password are present try and log in
            $rs = new C_RecordSet();
            // construct login sql using placeholders
            $loginSQL = "SELECT user_id, username, password FROM i_user WHERE user_id=:username";
                $params = array(':username' => $usr);
                $retval = $rs->getRecordSet($loginSQL, 'ResultSet', $params);
                // $retval pdo statement object
            $applicant = $retval->fetchObject();
            //check entered password against password from database
            if(password_verify($passw, $applicant->password)){
                // store successful login details
                $session->setProperty('username', $applicant->user_id);
                $rs = new JSONRecordSet();
                // create some sql to get the user who is $applicant->user_id
                $userSQL = "SELECT * FROM i_user WHERE user_id=$applicant->user_id ";

                $retval = $rs->getRecordSet($loginSQL, 'ResultSet', $params);
                echo $retval;
            } else { // if the log in failed unset any previously set value for user
                $session->removeKey('user');
                echo '{"status":{"error":"error", "text":"You have entered the incorrect login details."}}';
            }
        } else { // either username or password wasn't present so remove any previously set user values
            $session->removeKey('user');
            echo '{"status":{"error":"error", "text":"Username and Password are required."}}';
            }
        break;
    /**
     * return a json encapsulated user object if logged in else a json error message
     *
     */
    case 'loginStatus':
        $session = Session::getInstance();
        if ($session->getProperty('username') !== null) {
            $user = $session->getProperty('username');
            echo $user;
        }
        else {
            echo '{"status":{"error":"error", "text":"user not logged in"}}';
        }
        break;
    /**
     * remove the user session if logged in, return a json error message if not logged in
     */
    case 'logoutUser':
        $session = Session::getInstance();
        session_destroy();
        if ($session->removeKey('username')) {
            echo '{"status":{"text":"user logged out"}}';
        }
        else {
            echo '{"status":{"error":"error", "text":"no user logged in"}}';
        }
        break;
        //default action if no case is used
    default:
        echo '{"status":"error", "message":{"text": "default no action taken"}}';
        break;
}
