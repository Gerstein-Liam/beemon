/////////////////////////////////////////////////////////////////////////////////////////
//  Auteur: Gerstein Liam  (gerstein.liam@hotmail.com)
//  Autorité: EIA-FR
//  Date:23.6.2014           
//////////////////////////////////////////////////////////////////////////////////////////
//  fichier:gsm_controlleur.h
//  Include classe de pilotage du module GSM/GPRS SIM900
//  a) Initialisation module (ouverture du port, PIN)
//  b) Désactivation de l'écho provenant de la SIM900
//  c) Envoie de commandes et traitement des réponses
//  d) Fermeture du port série                    
///////////////////////////////////////////////////////////////////////////////////////////

#ifndef gsm_controlleur_h
#define gsm_controlleur_h

#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <windows.h>


//*****************************Désactiver l'écho série du module SIM900*******************/////
int disable_echo();
//*****************************Configuration et ouverture du port série*******************/////
extern int  init_gsm_serial() ;
//**************Envoie de la commande au module SIM900 et traitement de la réponse********/////
//  send_cmd(commande,reponse attendu,délai d'attente de la réponse,tampon de réception)
extern  int send_cmd (unsigned char* , unsigned char* , int ,char[]);
//*****************************************Fermeture du port série************************/////	
extern  int close_serial() ;    
#endif
