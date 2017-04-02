#include <stdio.h>
#include <windows.h>


struct ssc_config {
	int  f_ask_for_job;
	int  f_poll;
	
	
};

 
int WINAPI myThread(LPVOID struct  post_data) {
 
    //long double* var = (long double*) post_data;
  struct ssc_config *ssc_c =  ( struct ssc_config*) post_data;
     while (ssc_c->f_poll < 1000000000)
        (ssc_c->f_poll) ++;
 
    ExitThread(0);
 
}
 
int main() {
 
    int choix = 0;
    char szChoix[5] = "\0";
    long double var = 0;
	  long double var2 = 0;
struct ssc_config ssc_c; 
ssc_c.f_poll=0;
    SECURITY_ATTRIBUTES attr;
    HANDLE th = 0;
 
    attr.nLength = sizeof(SECURITY_ATTRIBUTES);
    attr.lpSecurityDescriptor = NULL;
    attr.bInheritHandle = 0;
 
    printf ("Valeur de var : %.0f\n", var);
    th = CreateThread(&attr, 0, myThread, &ssc_c, 0, NULL);
 
    do {
        printf ("1. Afficher la valeur de var\n");
        printf ("2. Quitter\n\n");
        printf ("Votre choix : ");
 
        fgets(szChoix, 5, stdin);
        choix = atoi(szChoix);
 
        if (choix == 1)
            printf ("Valeur de var : %.0f\n", ssc_c.f_poll );
    } while (choix != 2);
 
    return 0;
 
}