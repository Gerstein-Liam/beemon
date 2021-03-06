/////////////////////////////////////////////////////////////////////////////////////////
//  Auteur: Gerstein Liam  (gerstein.liam@hotmail.com)
//  Autorité: EIA-FR
//  Date:23.6.2014
//  Sources utilisées: 
//	    1)BGLIB+example de démonstration cette libraire ,l'example s'appelle HTM_COLLECTOR
//		Tout deux sont fourni par BLUEGIGA.L'exemple est dans le nouveau CD-ROM
//      2)Parseur tx_json "Frozen"
//////////////////////////////////////////////////////////////////////////////////////////
//  fichier:  main.c  
//  Base du relais de groupe, imcomplet mais permet de principalement de récupèrer les données de mesure
//  sur une ruche et de les déposer dans la BD (via une page PHP).La réponse contient une configuration simple
//   qui sera appliquee sur la ruche.En l'état le relais permet:
//  1) D'identifier le type d'advert BEEMON 
//  2) De lire/ecrire les mesures/configuration depuis/sur la ruche
//  3) Envoie de requete HTTP POST[tx_json] au SA.                       
//  4) Parsage de la reponse du SA contenant le rx_json(simple pour le moment et ne permet pas de parser
//     le JSON imaginé)
//  5) Rassemble ces 4 point pour créer une version tres simplifie du relais imaginé
//
//   Il manque: 
//	1) Lecture  et gestion des adresses MAC
//	2) Stockage temporaire des mesures dans fichiers
//	3) Un thread qui s'occupe de faire les requetes sur le SA 
//	4) Parsage complet de la réponse JSON, qui doit permettre de ressortir les configurations pour chaque ruche      
///////////////////////////////////////////////////////////////////////////////////////////

#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <ctype.h>
#include <string.h>
#include <math.h>
#include <time.h>

// Pilotage du module GSM/GPRS sim900				  (REALISE PAR L'ETUDIANT)
#include "gsm_controlleur.h"
// Parseur tx_json                                       (FOURNI PAR FROZEN)
#include "frozen.h"
// Definitions des commandes BGLIB                    (FOURNI PAR BLUEGIGA)
#include "cmd_def.h"
// Implémentation de la com série avec le Dongle112   (FOURNI PAR BLUEGIGA)
#include "uart.h"
// timeout de lecture com série DongleBLE112
#define UART_TIMEOUT 1000


// Decommenter pour debugger 
//#define DEBUG_BLE
//#define DEBUG_HTTP_CONTEXT

#define TEST_ENTIRE

//************************************Variables************************************
//Handle pour identifier communication BLE
uint8 connection_handle=0;
//Instantiation structure pour recevoir adresse BLE
bd_addr connect_addr;

//Structure pour les informations du relais 

struct beemon_header {
	unsigned char reason[12];
	unsigned char ssc_no_gsm[12];
};
struct beemon_header ssc_h;

//Structure pour les informations d'une ruche

struct beehives_header {
    unsigned char mac_adresse[20];
	int f_collecte;
	int f_proposition;
	unsigned char startdate_mesure[20];
	unsigned char enddate_mesure[20];
    unsigned char samplenumber[5];
};
struct beehives_header beehive_hd;

//  Pointeur pour la reponse HTTP que l'on ressort de la reponse  Brut
int p_http_response;

// Variable pour la lecture d'un tampon GATT
// Pointeur sur le message BLE contenant les données de la GATT
int p_ble_rx_data;
// Offset pour la chaine dedie a un seul echantillon
// Utilise car on ne peut que lire 22 Bytes
int offset_p_echantillon;  
int p_echantillon; //Pointeur pour la chaine de caractère dedié a un seul echantillon
int no_value;
// Structure permettant de recueillir les valeurs d'une date
struct tm instant;
// Variable de temps utilise pour ecrire dans la Gatt (obliger d'utiliser uint8)	
uint8 seconde;
uint8 minute;
uint8 heure;
uint8 jour;
uint8 mois;
uint8 annee;
//Attributs Gatt du SSI
uint16 conf_ssi_handle = 70,
fcollecte_ssi_handle = 19 ,
fproposition_ssi_handle = 16,
datetime_ssi_handle = 13,
capteur1_ssi_handle = 23,
depot_statut = 73
;

//Tampons données module GSM
char RX_DATA[1000]="";          // Tampon reponse entiere module GSM (ev. infos non voulues)
char HTTP_RESPONSE[1000]="";    // Tampon reponse HTTP issu du filtrage de RX_DATA
char POST_DATA[1000]="";        // Tampon d'envoie HTTP
//Tampons pour les mesures, le premier est pour un echantillon et l'autre pour 
// une collecte d'echantillon
char value[10]="";
char values[1000]="";

//************************************Définitions des états du relais*****************
// Etat de la communication BLE
typedef enum {
	state_disconnected,             // Etat deconnecte
	state_connected,                // Connecte
	state_finish                 // Fermer port série dongle
	// enum tail placeholder
} ble_com_contexts;
ble_com_contexts current_ble_com_context = state_disconnected;

// Etat Beemons
typedef enum {
	sleep,             // start/idle current_ble_com_context
	setup_ssi,collecte,
	interact_with_ssi,get_values_from_ssi
} beemon_context ;
beemon_context current_beemon_context = sleep;

// Etats pour le context Beemon "setup_ssi"
typedef enum {
	set_fcollecte,             // start/idle current_ble_com_context
	set_fproposition,set_c1_conf,set_time,set_conf_done,disconnect
} setup_context;
setup_context next_setup_context = set_fcollecte;


typedef enum {
	get_date,get_values_tampon1,  get_values_tampon2,            // start/idle current_ble_com_context
	valider_collecte,depot_BD,disconnect_depot,maj_configuration_effectuee,config_fproposition,config_fcollecte
} collecte_states;
collecte_states collecte_s = get_date;


//***************************************************************************************
//************************************Macro fonction pour coté GSM***********************
//***************************************************************************************

//Construis le JSON imaginé pour l'envoie des updates,mais pas tout les champs sont encore renseignees.
//Au final il y aura 2 fonctions, une qui s'occupe de créer pour chaque dépot de la part d'une ruche d'une structure
int ConstructJSONFromStructure(char * POST_DATA,struct  beemon_header ssc_h,char * values )
{

	
	unsigned char *tx_json [1000] ;
	
	unsigned char *facq_c [4];
	//sprintf(facq_c, "%d",ssi_c.f_acquisition);
	
	
	//"{\"no_gsm\" : \"0798698124\",\"reason\" : \"updates\",\"groupe\" : [{\"ruche\" : {\"mac_adr\" : \"A\",\"f_collecte\" : \"2\",\"f_proposition\" : \"3\",\"mesures_ruche\" : [{ \"capteur\" : {\"port\" : \"1\",\"date\" : \"2011-3-28 23:58\",\"mesures_capteur\" : [\"21.5C\", \"20.5C\", \"19.5C\",\"21.5C\", \"20.5C\", \"19.5C\",\"21.5C\", \"20.5C\", \"19.5C\",\"21.5C\", \"20.5C\", \"19.5C\"]}}]}}]}"
	//ConstructJSONFromStructure(POST_DATA,ssc_h);

	
	
	strcpy(tx_json ,"{");
	
	strcat(tx_json ,"\"no_gsm\":");
	strcat(tx_json ,"\"");
	strcat(tx_json ,ssc_h.ssc_no_gsm);
	strcat(tx_json ,"\"");

	
	strcat(tx_json ,",");
	
	strcat(tx_json ,"\"reason\":");
	strcat(tx_json ,"\"");
	strcat(tx_json ,ssc_h.reason);
	strcat(tx_json ,"\"");
	
	
	strcat(tx_json ,",");
	strcat(tx_json ,"\"groupe\" : [");
	strcat(tx_json ,	"{\"ruche\" :{ \"mac_adr\" :"); 
	strcat(tx_json ,"\"");
	strcat(tx_json ,beehive_hd.mac_adresse);
	strcat(tx_json ,"\"");
	strcat(tx_json ,",");
	strcat(tx_json ,"\"datedebut\" : ");
	strcat(tx_json ,"\"");
	strcat(tx_json ,beehive_hd.startdate_mesure);
	strcat(tx_json ,"\"");
	strcat(tx_json ,",");
	
	strcat(tx_json ,"\"datefin\" : ");
	strcat(tx_json ,"\"");
	strcat(tx_json ,beehive_hd.enddate_mesure);
	strcat(tx_json ,"\"");
	strcat(tx_json ,",");
	
	
	strcat(tx_json ,"\"nbre_echantillon\" : ");
	strcat(tx_json ,"\"");
	strcat(tx_json ,beehive_hd.samplenumber);
	strcat(tx_json ,"\"");
	strcat(tx_json ,",");
	
	strcat(tx_json ,"\"mesures_ruche\" : [");
	strcat(tx_json ,"{ \"capteur\" : {\"port\" : \"1\",");
	


	
	
	strcat(tx_json,"\"mesures_capteur\" : [");
	strcat(tx_json ,values);
	memset(values ,'\0',sizeof(values));
	strcat(tx_json ,"]}}");
	strcat(tx_json ,"]}}");
	strcat(tx_json ,"]}");
	
	
	printf("JSON_Sended:%s \n",tx_json);
	
	strcpy(POST_DATA,tx_json);

	


	return 0;
}
//Init module GSM (Com série, PIN)
int InitGSM ()
{
	init_gsm_serial();
	send_cmd("AT+IFC=1,1\r\n","OK",300,RX_DATA);
	send_cmd("AT\r\n","OK",100,RX_DATA);
	send_cmd("AT+CPIN=0000\r\n","Call",300,RX_DATA);
	return 0;

}
//Init context GPRS
int SetGPRSContext () 
{
	send_cmd("AT+SAPBR=3,1,\"Contype\",\"GPRS\"\r\n","OK",100,RX_DATA);
	send_cmd("AT+SAPBR=3,1,\"APN\",\"gprs.swisscom.ch\"\r\n","OK",100,RX_DATA);
	while(send_cmd("AT+SAPBR=1,1\r\n","OK",300,RX_DATA)!=1)
	{}
	while(send_cmd("AT+SAPBR=2,1\r\n","OK",300,RX_DATA)!=1)
	{}
	return 0;

}
//Envoie d'une requête post
int HttpPostRequest(char * POST_DATA)
{
	char json_value [100]="";

	send_cmd("AT+HTTPINIT\r\n","OK",100,RX_DATA);
	send_cmd("AT+HTTPPARA=\"CID\",1\r\n","OK",100,RX_DATA);
	send_cmd("AT+HTTPPARA=\"URL\",\"beemon02.tic.eia-fr.ch/ssc_conn_point.php\"\r\n","OK",100,RX_DATA);
	send_cmd("AT+HTTPPARA=\"CONTENT\",\"application/json\"\r\n","OK",100,RX_DATA);
	


	
	//static const char *tx_json = " { reason: \"stop_poll\"} ";
	printf(POST_DATA);
	printf("\n");
	//static const char *tx_jso
	
	
    // Construction de la commande AT+HTTPDATA
	int post_data_length=strlen(POST_DATA);
	unsigned char *post_data_length_c [4];
	
	sprintf(post_data_length_c, "%d",post_data_length);
	int size_of_size=strlen(post_data_length_c);
	
	unsigned char *att_post_para[22+size_of_size];
	strcpy(att_post_para ,"AT+HTTPDATA=");
	strcat(att_post_para, post_data_length_c);
	strcat(att_post_para,",120000\r\n");
	send_cmd(att_post_para,"DOWNLOAD",3000,RX_DATA);
	#ifdef DEBUG_HTTP_CONTEXT
	printf("TAILLE:%d  \n ", post_data_length);
	#endif
	
	send_cmd(POST_DATA ,"OK",800,RX_DATA);   // Valeur du HTTP Post
	memset(POST_DATA ,'\0',sizeof(POST_DATA));
	
    // On reesayer d'envoie s'il y a une un timeout
	while(	send_cmd("AT+HTTPACTION=1\r\n","+HTTPACTION:1,200",500,RX_DATA)!=1)
	{}
	
	send_cmd("AT+HTTPREAD=0,507\r\n","OK",2000,RX_DATA);
	// Variable pour la récuperation de la reponse
	int p_gsm_rx_data_brut;
	memset(HTTP_RESPONSE,'\0',sizeof(HTTP_RESPONSE));
	int begin_of_json_string_catched=0;
	int p_http_response=0;
	for( p_gsm_rx_data_brut=0;p_gsm_rx_data_brut<1000;p_gsm_rx_data_brut++)
	{
		
		if(RX_DATA[p_gsm_rx_data_brut]=='{' || begin_of_json_string_catched==1 || RX_DATA[p_gsm_rx_data_brut]=='}' )
		{
			HTTP_RESPONSE[p_http_response]= RX_DATA[p_gsm_rx_data_brut];
			p_http_response++;
			#ifdef DEBUG_HTTP_CONTEXT
			printf("%c", RX_DATA[p_gsm_rx_data_brut]);
			#endif
			begin_of_json_string_catched=1;
			if(RX_DATA[p_gsm_rx_data_brut]=='}'){begin_of_json_string_catched=0;}
		}
		
	}
	
	printf("  JSON_Received :%s \n", HTTP_RESPONSE);
	// Si il y a un JSON ({) on le parse et on recupere les valeurs de config de la ruche
	if(strstr(HTTP_RESPONSE,"{")!=0){
	struct json_token *arr, *tok;
	// Tokenize POST_DATA string, fill in tokens array
	arr = parse_json2(HTTP_RESPONSE, strlen(HTTP_RESPONSE));
	// Search for parameter "bar" and print it's value
	tok = find_json_token(arr, "f_collecte");
	
	strncpy(json_value,tok->ptr,tok->len);
	printf("JSON_Received_Value:%s\n",json_value);

    beehive_hd.f_collecte = atoi(json_value);
	tok = find_json_token(arr, "f_proposition");
	
	strncpy(json_value,tok->ptr,tok->len);
	printf("JSON_Received_Value:%s\n",json_value);
	
	beehive_hd.f_proposition = atoi(json_value);
	free(arr);}
	
	send_cmd("AT+HTTPTERM\r\n","OK",2000,RX_DATA);
	Sleep(2000);
	return 0;
}

//***************************************************************************************
//************************************Fonction pour les adresse BLE**********************
//***								 *(FOURNI PAR BLUEGIGA)*						 ****

//Compare 2 adresse bluetooth
int cmp_bdaddr(bd_addr first, bd_addr second)
{
	int i;
	for (i = 0; i < sizeof(bd_addr); i++) {
		if (first.addr[i] != second.addr[i]) return 1;
	}
	return 0;
}
//Afficher adresse bluetooth
void print_bdaddr(bd_addr bdaddr)
{
	printf("%02x:%02x:%02x:%02x:%02x:%02x",
	bdaddr.addr[5],
	bdaddr.addr[4],
	bdaddr.addr[3],
	bdaddr.addr[2],
	bdaddr.addr[1],
	bdaddr.addr[0]);
}

//***************************************************************************************
//************************************Fonction communication série BLE*******************
//***								 *(FOURNI PAR BLUEGIGA)*						 ****

void print_raw_packet(struct ble_header *hdr, unsigned char *data, uint8 outgoing)
{
	int i;
	printf(outgoing ? "TX => [ " : "RX <= [ ");
	for (i = 0; i < 4; i++) {   
		// Affiche les 4 bytes identifiant la commande, ou réponse, ou événement
		printf("%02X ", ((unsigned char *)hdr)[i]);
	}
	if (hdr -> lolen > 0) {
		// Affiche les  bytes de données de la commande, ou réponse, ou événement
		printf("| ");          
		for (i = 0; i < hdr -> lolen; i++) {
			printf("%02X ", ((unsigned char *)hdr)[i + 4 ]);
		}
	}
	printf("]\n");
}

void send_api_packet(uint8 len1, uint8* data1, uint16 len2, uint8* data2)
{
	#ifdef DEBUG_BLE
	// Affiche les paquet sortant
	print_raw_packet((struct ble_header *)data1, data2, 1);
	#endif

	// Envoie des paquet a l'UART
	if (uart_tx(len1, data1) || uart_tx(len2, data2)) {
		// uart_tx returns non-zero on failure
		printf("ERROR: Writing to serial port failed\n");
		exit(1);
	}
}


int read_api_packet(int timeout_ms)
{
	unsigned char data[256]; // enough for BLE
	struct ble_header hdr;
	int r;

	//Réception des paquet provenant de l'UART
	r = uart_rx(sizeof(hdr), (unsigned char *)&hdr, timeout_ms);
	if (!r) {
		return -1; // Time out expiré
	}
	else if (r < 0) {
		printf("ERROR: Reading header failed. Error code:%d\n", r);
		return 1;
	}

	if (hdr.lolen) {
		r = uart_rx(hdr.lolen, data, timeout_ms);
		if (r <= 0) {
			printf("ERROR: Reading data failed. Error code:%d\n", r);
			return 1;
		}
	}

	// Utilise la fonction BGlib pour créer la bonne structure "ble-msg" basé sur l'entête (hdr)
	const struct ble_msg *msg = ble_get_msg_hdr(hdr);

	#ifdef DEBUG_BLE
	// Affiche les paquet entrant
	print_raw_packet(&hdr, data, 0);
	#endif

	if (!msg) {
		printf("ERROR: Unknown message received\n");
		exit(1);
	}

	// Appel la fonction approprié avec n'importe quel charge de donnée
	// C'est ici que l'on associe les paquet entrant a la bonne fonction BLE (response, event)
	msg -> handler(data);

	return 0;
}

//***************************************************************************************
//***********Event et réponses genérés par le module Bluetooth***************************
//***************************************************************************************

void ble_evt_gap_scan_response(const struct ble_msg_gap_scan_response_evt_t *msg)
{
	printf("---------------------Annonce recu \n");

	printf(" \n advert data-> %02hx \n  ",msg->data.data);
	if(msg->data.data[11]==0x01){
		printf(" \n Raison d'advertiser: Mise en service -> %02hx \n  ",msg->data.data[11]);
		printf(" \n Adresse de la ruche:  \n  ",msg -> sender);
		ble_cmd_gap_connect_direct(msg->sender.addr, msg-> address_type, 32, 48, 100, 0);
		current_beemon_context = setup_ssi;
		next_setup_context = set_fcollecte;
		printf("\n--------------------------ADV-MISE EN SERVICE------------------------------------- \n \n");
	}
	
	
	if(msg->data.data[11]==0x10){
		printf(" \n Raison d'advertiser: Propon  -> %02hx \n  ",msg->data.data[11]);
			printf(" \n Adresse de la ruche:  \n  ",msg -> sender);
		//ble_cmd_gap_connect_direct(msg->sender.addr, msg-> address_type, 32, 48, 100, 0);
		//current_beemon_context = setup_ssi;
		printf("\n------------------------ADV PROPOSITION------------------------------------- \n \n");
	}
	
	if(msg->data.data[11]==0x00){
		printf(" \n Raison d'advertiser: depot -> %02hx \n  ",msg->data.data[11]);
			printf(" \n Adresse de la ruche:  \n  ",msg -> sender);
		ble_cmd_gap_connect_direct(msg->sender.addr, msg-> address_type, 32, 48, 100, 0);
		current_beemon_context = collecte;
		collecte_s = get_date;
		printf("\n-------------------------ADV---DEPOT------------------------------------- \n \n");
	}
	
}

void ble_evt_connection_status(const struct ble_msg_connection_status_evt_t *msg)
{

	
	current_ble_com_context=state_connected;
	connection_handle=msg->connection;
}


void ble_evt_connection_disconnected(
const struct ble_msg_connection_disconnected_evt_t * msg
)
{
	printf("\n-------------------------------------------------------- \n \n");
	current_ble_com_context=state_disconnected;

	ble_cmd_gap_discover(gap_discover_generic);

}



void ble_evt_attclient_attribute_value(const struct ble_msg_attclient_attribute_value_evt_t *msg)
{
	
	//printf ("ble-msg: %p_gsm_rx_data_brut, %p_gsm_rx_data_brut \n", &msg->connection, &msg->atthandle[0]);
	p_echantillon=offset_p_echantillon;

	if(current_ble_com_context == state_connected){
		//Lecture Collecte
		if(current_beemon_context == collecte){	
			switch (collecte_s ){
				
			//Récupération date de début	
				
			case get_date : 
				
				collecte_s=get_values_tampon1;
				
				
				
				////////////////////////////////////////////////////DATE FIN/////////////////////////////////
				int seconde=msg->value.data[6];
				int minute=msg->value.data[5];
				int heure=msg->value.data[4];
				int jour=msg->value.data[2];
				int mois=msg->value.data[1];
				int annee=msg->value.data[0];
				annee=annee+1900;
				unsigned char date_echantillon [30];
			    unsigned char heure_s [20];
			    unsigned char minute_s [20];
				unsigned char seconde_s [20];
			
			
			
			    sprintf(date_echantillon, "%d-%d-%d ",annee, mois,jour);

				if(heure<10){
				sprintf(heure_s, "0%d:",heure);
	            }
				else
				{sprintf(heure_s, "%d:",heure);}
				
				if(minute<10){
				sprintf(minute_s, "0%d:",minute);
	            }
				else
				{sprintf(minute_s, "%d:",minute);}
				
				
				if(seconde<10){
				sprintf(seconde_s, "0%d",seconde);
	            }
				else
				{sprintf(seconde_s, "%d",seconde);}
				

				
				strcat(date_echantillon ,heure_s);
				strcat(date_echantillon ,minute_s);
				strcat(date_echantillon ,seconde_s);
				
				strcpy(beehive_hd.startdate_mesure,date_echantillon);
			
				/////////////////////////////////////////////////////DATE FIN////////////////////////////////
				time_t secondes;
				struct tm instant;
				time(&secondes);
				instant=*localtime(&secondes);
                sprintf(date_echantillon, "%d-%d-%d ",instant.tm_year+1900,instant.tm_mon+1,instant.tm_mday);

				if(instant.tm_hour<10){
				sprintf(heure_s, "0%d:",instant.tm_hour);
	            }
				else
				{sprintf(heure_s, "%d:",instant.tm_hour);}
				
				if(instant.tm_min<10){
				sprintf(minute_s, "0%d:",instant.tm_min);
	            }
				else
				{sprintf(minute_s, "%d:",instant.tm_min);}
				
				if(instant.tm_sec<10){
				sprintf(seconde_s, "0%d",instant.tm_sec);
	            }
				else
				{sprintf(seconde_s, "%d",instant.tm_sec);}
				
				strcat(date_echantillon ,heure_s);
				strcat(date_echantillon ,minute_s);
				strcat(date_echantillon ,seconde_s);
		
				seconde=instant.tm_sec;
				minute= instant.tm_min;
				heure= instant.tm_hour;
				jour= instant.tm_mday;
				mois=instant.tm_mon+1;
				annee=instant.tm_year;
				uint8 date_ssi_format[] = { annee,mois,jour,0,heure,minute,seconde}; // "DONE"
				
				strcpy(beehive_hd.enddate_mesure,date_echantillon);
				ble_cmd_attclient_attribute_write(connection_handle,datetime_ssi_handle,7, &date_ssi_format);
				
			break;
				
			//                      Lecture tampon	
		    // Récupération des données apres commande READLONG	
			// Un Read long peut lire jusqu'a 255 Byte
			// Mais la longueur maximum transmissible en une seul fois 
			// est de 22 Bytes
			//Cette event va donc se réveiller plusieurs fois après
			// une seul commande ReadLong
			case get_values_tampon1 : 
				//On regarde si c'est la dernière transmission
				//de données, si c'est inférieur a 22 Byte 
				// cela veut dire qu'il s'agit de la dernière partie
				// du champs tampon 1
				//EX: Si tampon contient 2*22Bytes->Cette event est reveillé 2 fois
				//    Si tampon contient 2*22Bytes + 1-> reveillé 3 fois
				if(msg->value.len < 22)
				
				{ collecte_s = valider_collecte;
				 
				}
				else
				{collecte_s = get_values_tampon1;}
				p_ble_rx_data=0;
				
				for (; p_ble_rx_data <msg->value.len; p_ble_rx_data++)        //i <msg->value.len
				{
					// Le format utilisé pour les mesures est :25.5C soit 5 Bytes
					//On veut isoler les échantillons pour pouvoir construire le JSON
					if (p_echantillon<5)
					{   
						value[p_echantillon]=msg->value.data[p_ble_rx_data];
						//printf("%c",msg->value.data[i]);
						
						p_echantillon++;
					}
					else
					{
						printf("No valeur: %d  Valeur : %s  \n",no_value,value);
						
						strcat(values ,"\"");
						strcat(values ,value);
						strcat(values ,"\"");
						
						if(collecte_s == valider_collecte& p_ble_rx_data>msg->value.len-5){}
						else
						{strcat(values ,",");}
						
						no_value=no_value+1;
						
						p_echantillon=0;
					}	
				}	
				if (p_echantillon<5)
				{   //Comme 22Byte%5byte=2
					//On doit retenir la valeur du pointeur d'échantillons
					//Pour le prochain réveil
					offset_p_echantillon=p_echantillon;	
				}
				break;
				default : break;
			}
			
		}
		
	}
	
}

//***************************************************************************************
//***********************************MACHINE D'ETAT SSC**********************************
//***************************************************************************************

void ble_evt_hardware_soft_timer(const struct ble_msg_hardware_soft_timer_evt_t * msg)
{
if(current_beemon_context != sleep){

	//MachineEtatMiseEnService
	
	if(current_beemon_context == setup_ssi){	
		
		switch (next_setup_context){
			
		case set_fcollecte : 
			printf("-------------------Ecriture Configuration frequence collecte \n");
			next_setup_context=set_fproposition;
			
			uint8 f_collecte[] = {beehive_hd.f_collecte}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle, fcollecte_ssi_handle, 1, &f_collecte);
			break;
			
		case set_fproposition : 
			
			printf("---------------------Ecriture Configuration proposition \n");
			next_setup_context=set_c1_conf;
			uint8 f_proposition[] = {beehive_hd.f_proposition}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle, fproposition_ssi_handle, 1, &f_proposition);

			break;
			
		case set_c1_conf : 
			printf("---------------------Ecriture Configuration capteur 1 \n");
			next_setup_context=set_time;
			uint8 c1[] = {0x55,0x53,0x49,0x32,0x54,0x45}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle,  capteur1_ssi_handle,6, &c1);
			break;
			
		case set_time : 
			printf("---------------------Ecriture Date  \n");
			next_setup_context=set_conf_done;
			
			time_t secondes;
			struct tm instant;
			time(&secondes);
			instant=*localtime(&secondes);
			
			unsigned char date_echantillons [30];
			sprintf(date_echantillons, "%d-%d-%d %d:%d:%d",instant.tm_year+1900,instant.tm_mon+1,instant.tm_mday,instant.tm_hour,instant.tm_min);
			//int size_of_size=strlen(post_data_length_c);
			
			printf("%s",date_echantillons);
			
			uint8 size_of_date=strlen(date_echantillons);		
			
			seconde=instant.tm_sec;
			minute= instant.tm_min;
			heure= instant.tm_hour;
			jour= instant.tm_mday;
			mois=instant.tm_mon+1;
			annee=instant.tm_year;
			uint8 date_ssi_format[] = { annee,mois,jour,0,heure,minute,seconde}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle,datetime_ssi_handle,7, &date_ssi_format);
			break;		
			
			
		case set_conf_done : 
			printf("---------------------Ecriture Validation configuration \n");
			uint8 configuration[] = {0x44,0x4f,0x4e,0x45}; // "DONE"
			next_setup_context=disconnect;
			ble_cmd_attclient_attribute_write(connection_handle, conf_ssi_handle, 4, &configuration);
			break;
			
		case disconnect : 
			printf("---------------------Deconnexion \n");
			ble_cmd_connection_disconnect(0); 
			current_beemon_context = sleep;
			break;
			
			default : break;
		}
		
	}
	
	
	

	
	
	if(current_beemon_context == collecte){	
		
		
		switch (collecte_s ){
			
		case get_date : 
			ble_cmd_attclient_read_long(connection_handle,13);
			
			
			break;
		case get_values_tampon1 : 
			offset_p_echantillon=0;
			no_value=0;
			ble_cmd_attclient_read_long(connection_handle,29);
			break;
		case valider_collecte : 
			collecte_s=config_fcollecte;
			sprintf(beehive_hd.samplenumber, "%d",no_value);
			printf("Nbre echantillon=%d",beehive_hd.samplenumber);
			ble_cmd_attclient_attribute_write(connection_handle,depot_statut, 4, "DEPD");
		break;
		case config_fcollecte : 
		collecte_s=config_fproposition;
			printf("-------------------Ecriture Configuration frequence collecte \n");
			uint8 f_collecte[] = {beehive_hd.f_collecte}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle, fcollecte_ssi_handle, 1, &f_collecte);
			break;
		case config_fproposition : 
			collecte_s=maj_configuration_effectuee;
			printf("---------------------Ecriture Configuration proposition \n");
			next_setup_context=set_c1_conf;
			uint8 f_proposition[] = {beehive_hd.f_proposition}; // "DONE"
			ble_cmd_attclient_attribute_write(connection_handle, fproposition_ssi_handle, 1, &f_proposition);
		break;
		case maj_configuration_effectuee : 
		   collecte_s=disconnect_depot ;
			printf("---------------------Ecriture Validation configuration \n");
			uint8 configuration[] = {0x44,0x4f,0x4e,0x45}; // "DONE"
			next_setup_context=disconnect;
			ble_cmd_attclient_attribute_write(connection_handle, conf_ssi_handle, 4, &configuration);
			break;
		case disconnect_depot : 
			printf("---------------------Deconnexion \n");
			collecte_s=depot_BD;
			ble_cmd_connection_disconnect(0); 
			break;
		case depot_BD : 
			ble_cmd_gap_set_mode(0, 0);
			collecte_s=disconnect_depot;
			printf("\n------------------------INIT GSM----------------------------- \n \n");
		
			strcpy(ssc_h.reason,"updates");
			strcpy(ssc_h.ssc_no_gsm,"0798698124");
			printf("no gsm: %s",ssc_h.ssc_no_gsm );

			printf("---------------------HTTPPOST \n");
			ConstructJSONFromStructure(POST_DATA,ssc_h,values);
			
			HttpPostRequest(POST_DATA);
			current_beemon_context = sleep;
		    ble_cmd_gap_discover(gap_discover_generic);
			break;
			default : break;
		}
		
	}
}
	
}

//***************************************************************************************
//************************************MAIN***********************************************
//***************************************************************************************

int main(int argc, char *argv[])

{
    //Init module GSM et mise en service du GPRS
    InitGSM ();
    SetGPRSContext ();
	//Valeur par défaut pour la mise en service
	beehive_hd.f_collecte=1;
    beehive_hd.f_proposition=2;
	//Valeur par défaut car récupération adresse MAC pas faite
	strcpy(beehive_hd.mac_adresse,"00:07:80:67:07:44");
	
	char *uart_port;
	
	// L'arguments présent?
	if (argc <= 1)
	{
		return 1;
	}
	else
	{
		uart_port = argv[1];
	}
	// Associe le pointeur de fonction pour les commandes (sorties) à la fonction "send_api_packet"
	bglib_output = send_api_packet;

	// open the serial port
	if (uart_open(uart_port)) {
		printf("ERROR: Unable to open serial port\n");
		return 1;
	}
	printf("\n------------------------INIT BLE----------------------------- \n \n");
	#if 1 // Reset léger
	// Fermeture connexions, arret scanning
	printf("---------------------Reset dongle \n");
	ble_cmd_connection_disconnect(0);
	ble_cmd_gap_set_mode(0, 0);
	ble_cmd_gap_end_procedure();
	ble_cmd_hardware_set_soft_timer(0,1,0);

	#else // Reset matériel

	ble_cmd_system_reset(0);
	uart_close();
	do {
		usleep(500000); // 0.5s
	} while (uart_open(uart_port));
	#endif

	printf("---------------------Lance scanning  \n");
	ble_cmd_gap_discover(gap_discover_generic);
	printf("---------------------Lance timer machine d'état \n");
	ble_cmd_hardware_set_soft_timer(32768*2,1,0);
	
	//Lire l'entrée série dongle BLE jusqu'a demande arrêt
	while (current_ble_com_context != state_finish)
	{
		//Lire l'entrée série dongle BLE
		if (read_api_packet(UART_TIMEOUT) > 0) break;
	}

	// Fermer port série dongle
	uart_close();
	return 0;
	
	
	
}
