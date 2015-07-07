<?
class MysqlDatabase{
	
	private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host;
	private $pdo;
        private $totale;
        private $utf8;
	
	
	public function __construct($db_name,$db_user='root',$db_pass='',$db_host='localhost',$utf8=false){
		
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_host = $db_host;
                $this->utf8 = $utf8;
	}

	public function getPDO(){
		if(is_null($this->pdo)){
                        $dsn = 'mysql:host='.$this->db_host.';dbname='.$this->db_name;
			$pdo = New PDO($dsn, $this->db_user, $this->db_pass);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo = $pdo;
		}
               if($this->utf8==true){
                        $this->pdo->exec("SET CHARACTER SET utf8");

                }
		return $this->pdo;
	}

    /** reqête non préparée
     * @param $statement
     * @param null $class_name
     * @param bool $one
     * @return array|mixed
     */
    public function query($statement, $class_name = null ,$one = false){
			$req = $this->getPDO()->query($statement);
		        if($class_name == null){
		            $req->setFetchMode(PDO::FETCH_OBJ);
		        } else {
		            $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
		        }
		        if($one){
		            $data = $req->fetch();
		        } else {
		            $data = $req->fetchAll();
		        }
		        $this->totale = $req->rowCount();
	 return $data;
	}
    public function get_nb(){
        return ($this->totale == null ? 0: $this->totale);
    }

    /** encodage des resultats d'une requête en json
     * @param $statement
     * @param bool $encode
     * @return array|string
     */
    public function json_query($statement,$encode=true){
		        $req = $this->getPDO()->query($statement);
		        $req->setFetchMode(PDO::FETCH_ASSOC);
		        $data = $req->fetchAll();
		        if($encode)
		        	return json_encode($data);
		        else
		          	return $data;
    }

    /** requête préparée
     * @param $statement
     * @param $attributes
     * @param null $class_name
     * @param bool $one
     * @return array|bool|mixed
     */
    public function prepare($statement, $attributes, $class_name = null, $one = false){
			$req = $this->getPDO()->prepare($statement);
			$res = $req->execute($attributes);
	        if(
	            strpos($statement, 'UPDATE') === 0 ||
	            strpos($statement, 'INSERT') === 0 ||
	            strpos($statement, 'DELETE') === 0
	        ){
	            return $res;
	        }
			if($class_name == null){
				$req->setFetchMode(PDO::FETCH_OBJ);
			} else {
				$req->setFetchMode(PDO::FETCH_CLASS, $class_name);
			}
			if($one){
				$data = $req->fetch();
			} else {
				$data = $req->fetchAll();
			}
			
	return $data;
	}
	
}