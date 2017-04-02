<?php
class requests_handlers{
	public $rx_json ;
	public $DB_NAME = 'beemon_bd';
	public $DB_HOST = 'localhost';
	public $DB_USER = 'root';
	public $DB_PASS = '';
	public $mysqli;

	public $relais_infos = array(
	"reason" => "",
	"no_gsm"   => "",
	"config"   => array(
	"f_polling"=>""
	));
	
	
	function ConnectToDatabase()
	{	
		$this->mysqli     = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}}			  

	function CreateJSONfromBody()
	{
		$this->rx_json = json_decode(stripslashes(file_get_contents("php://input")),true);
		
	}
	
	function   GetRelaisInfos() { 
		$this->relais_infos["reason"]=$this->rx_json['reason'];
		$this->relais_infos["no_gsm"]=$this->rx_json['no_gsm'];
	}


	function DoUpdates() { 
		$query= "SELECT id_groupe FROM `relai_groupe` WHERE no_gsm='" .  $this->relais_infos["no_gsm"] . "'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc()) 
			{$id_groupe=$row["id_groupe"];}
			
			
			
			
			
			
			
			foreach($this->rx_json['groupe'] as $groupe=>$ruche){ 

				foreach($ruche as $ruche=>$data_ruche){
				
					$mac_adresse=$data_ruche['mac_adr'];
					$nombre_echantillons=$data_ruche['nbre_echantillon'];
					$date_debut_echantillon=$data_ruche['datedebut'];
					$date_fin_echantillon=$data_ruche['datefin'];
					echo $mac_adresse;

					
		//$query="SELECT `frequence_collecte`,`frequence_proposition` FROM `ruche_equipee` WHERE `adr_mac`= '00:07:80:67:07:44'";
		$query="SELECT `frequence_collecte`,`frequence_proposition` FROM `ruche_equipee` WHERE `adr_mac`= '" .$mac_adresse . "'";
				    $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
					
					if($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
						
				     echo '{';
				     echo '"f_collecte" : "' . $row['frequence_collecte'] .'" ' ;
  				     echo ',' ;
				      echo '"f_proposition" : "' . $row['frequence_proposition'] .'" ' ;
  				     echo '}';

				    
					}
					}
									
				
					echo "  Date debut: " . 	$date_debut_echantillon . "  Date fin: " . 	$date_fin_echantillon . "  Nombre echantillons: " . $nombre_echantillons;
                    echo "  Echantillons: ";
					//	echo $date_debut_echantillon;
					$format = 'Y-m-d H:i:s';
					$date_debut_ech = DateTime::createFromFormat($format,$date_debut_echantillon);
					//	echo "Format: $format; " . $date->format('i') . "\n";
                    $date_fin_ech = DateTime::createFromFormat($format,$date_fin_echantillon);                                   
					$diff =  $date_fin_ech->getTimestamp() -  $date_debut_ech->getTimestamp();
						//$interval =date_diff($date_debut_ech,$date_fin_ech);
					//echo $interval->format('%R%a days');
					$diff=$diff/6;
					$diff=round($diff);
					$interval="PT".$diff."S";
				
			
					$date_echantillon=$date_debut_ech;

			
				
					
			
					foreach($data_ruche['mesures_ruche'] as $mesures=>$capteurs){ 
						foreach($capteurs as $capteurs=>$capteur){ 

	                                          
							
							foreach($capteur['mesures_capteur'] as $mes_capteur=>$valeur){ 
								
								
						    	$date_echantillon ->add(new DateInterval($interval));
								$date_echantillon_s = $date_echantillon->format('Y-m-d H:i:s');
							//	echo $date_echantillon_s;
					
								
							//	$date_echantillon=$annee."-".$mois."-".$jour." ".$heure.":".$minutes.":".$seconde;
							//   echo  $valeur . "  ";
								
								
							   $query="INSERT INTO `echantillon_mesure`(`id_ech`, `fk_id_groupe`, `unite_physique`, `valeur_echantillon`, `date_echantillon`) VALUES ('','" .  $id_groupe . "','','" .  $valeur . "', '" .  $date_echantillon_s . "')";
							   $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
								
								
								
							
								
							}
						}
					} 
				}
			}
			

			$query= "SELECT * FROM `communications` WHERE fk_id_groupe='" .  $id_groupe . "'";
			$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc()) 
				{
				
				
				
				//echo "user want monitor in direct";
				
				
				
				}
				
				
				
				$query="UPDATE `communications` SET `conn_status`='conn' WHERE fk_id_groupe='" .  $id_groupe . "'";
				$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			}
			else
			{
				
			}
		}
		else
		{
			
		}
	}
}

?>
<?php
error_reporting(0);
$r_handlers = new requests_handlers();
$r_handlers->ConnectToDatabase();

$r_handlers->CreateJSONfromBody();
$r_handlers->GetRelaisInfos();

if($r_handlers->relais_infos["reason"]=="updates")
{
	$r_handlers->DoUpdates();

}
?>
			
