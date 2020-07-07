<?
/**
* Class Reports
*/
class Reports
{
	public $isConn;
	protected $db;
	public function __construct($username = "db_username", $password="db_password", $servername = "localhost", $dbName = "sm") {
		$this->isConn = true;
		try {
			$this->db = new mysqli($servername, $username, $password, $dbName);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	public function Disconnect(){
		$this->db = null;
		$this->isConn = false;
		echo "Disconnected\n";
	}
	function getReport($query, $params = []){
		try {
			$result = $this->db->query($query);
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				// $row = mysqli_fetch_assoc($result);
				// print_r($row);
				return $row;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	public function getRows($query, $params = []){
		try {
			$result = $this->db->query($query);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
						$id = $row['id'];
						$date = $row['date'];
						$user = $row['user_id'];
						$category = $row['category'];
						$content = nl2br($row['content']);
						// $r[$date][] = "$id|$date|$user|$category|$content";
						// $r = "$id|$date|$user|$category|$content";
					$r[] = $row;
				}
				$result = $r;
			}
			else{
				$result = "0 rows";
			}
			return $result;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	//Insert new row in DB;
	public function insertRow($query){
		try {
			if ($this->db->query($query) === TRUE) {
				return true;
			} else {
				// echo "Error: " . $sql . "<br>" . $conn->error;
				return false;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	function updateRow($query){
		try {
			if ($this->db->query($query) === TRUE) {
				return true;
			} else {
				// echo "Error: " . $sql . "<br>" . $conn->error;
				return false;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	function deleteRow($query){
				try {
			if ($this->db->query($query) === TRUE) {
				return true;
			} else {
				// echo "Error: " . $sql . "<br>" . $conn->error;
				return false;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}

?>