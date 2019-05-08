<?php
require_once "model/Rating.php";
require_once "lib/lib.php";


class Compete {

	public $user_id;
	public $shown_res=array();
	public $state="";
	protected $_raingA;
	protected $_ratingB;

	public function __construct($id) {
		$this->user_id = $id;
		

	}
	public function get_restaurants(){
		$restaurants = file("dev/restaurants.txt");
		$val1 = "";
		$val2 = "";
		$res_pair = array(&$val1,&$val2);
		$res_pair2 = array(&$val2,&$val1);
		$distinct = FALSE;

		while($distinct == FALSE){
			$val1 = $restaurants[rand(0, count($restaurants)-1)]; 
			$val2 = $restaurants[rand(0, count($restaurants)-1)];
			if($val1 != $val2 && !in_array($res_pair,$this->shown_res) && !in_array($res_pair2,$this->shown_res)){
				$user = $_SESSION['Compete']->user_id;
				$query = "SELECT * FROM voterecord WHERE id=$1 AND ((field1='$val1' AND field2='$val2') OR (field1='$val2' AND field2='$val1'));";
				pg_query($_SESSION['Connection'], "DEALLOCATE ALL");
				$result = pg_prepare($_SESSION['Connection'],"query",$query);
				$result = pg_execute($_SESSION['Connection'],"query",array($_SESSION['Compete']->user_id));		
				$row = pg_num_rows($result);
				
				if($row == 0){

					$distinct = True;
					$val1 = str_replace("''", "'", $val1);
					$val2 = str_replace("''", "'", $val2); 
					array_push($this->shown_res,$res_pair2); 
					array_push($this->shown_res,$res_pair);
				}
			}
		}
	}
	public function make_vote($ratingA,$ratingB,$scoreA,$scoreB){
		$rating = new Rating($ratingA,$ratingB,$scoreA,$scoreB);
		$result = $rating->getNewRatings();
		return $result;
	}
	public function set_state($state){
		$this->state = $state;
	}
	public function get_state(){
		return $this->state;
	}
	
	public function history(){
		return end($this->shown_res);
	}

}
?>


