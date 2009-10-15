<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/  
    define ('AUTHENTICATION_REGISTER_URL',			'Inregostreaza-te');
	define ('AUTHENTICATION_ACTIVATE_URL',			'Activeaza');
    define ('ADMIN_LOGIN_FORM',                     'Administrator! Neautentificat!');
    define ('ADMIN_USERNAME',                       'Utilizator:');
    define ('ADMIN_PASSWORD',                       'Parola:');
    define ('ADMIN_LOGIN_GO',                       'Intra!');
    define ('ADMIN_ACCESS_DENIED',                  'Nu s-a putut face autentificarea! Acces interzis!');
    define ('MANAGE_ZONES',                         'Zone');
    define ('MANAGE_GROUPS',                        'Grupuri');
    define ('MANAGE_USERS',                         'Utilizatori');
    define ('MANAGE_ZONES_MAPPING',                 'ACL-uri pentru grupuri');
    define ('MANAGE_ZONES_MAPPING_FOR_USERS',       'ACL-uri pentru utilizatori');
    define ('ADMIN_MANAGE_CONFIGURATION',           'Configuratie');
    define ('CANNOT_ERASE_ADMIN_GROUP',             'Nu puteti sterge grupul de administratori! Asta ar distruge totul!');
    define ('CANNOT_DELETE_GROUP_WITH_USERS',       'Nu puteti sterge un grup care contine utilizatori! Ne pare rau!');
    define ('CANNOT_DELETE_ADMINISTRATOR_USER',     'Nu va putem permite sa stergeti utilizatorul administrator. Ne pare rau!');
    define ('USERS',                                'Utilizatori');
    define ('GROUP_ALREADY_EXISTS',                 'Grupul deja existent! Grupurile nu pot avea acelasi nume simultan!');
    define ('GROUP_NAME_CANNOT_BE_EMPTY',           'Numele grupului nu poate fi gol! Complletati un nume pentru grup!');
    define ('ADD_NEW_GROUP_OF_USERS',               'Adaugati un grup nou');
    define ('ADMIN_GROUP_NAME_LABEL',               'Numele grupului');
    define ('ADMIN_AS_A',                           'Ca si');
    define ('ADMIN_OF_GROUP',                       'Al grupului');
    define ('ADMIN_GROUP_CHILD',                    'prima subcategorie');
    define ('ADMIN_GROUP_LAST_CHILD',               'ultima subcategorie');
    define ('ADMIN_GROUP_BROTHER',                  'precedentul brother');
    define ('ADMIN_GROUP_NEXT_BROTHER',             'urmatorea categorie');
    define ('ADMIN_GROUP_MOVED_TO_CHILD',           'Grupul nu poate fi mutat intr-o subcategorie a aceteia!');
    define ('ADMIN_ADD_GROUP',                      'Adaugare grup');
    define ('ADMIN_EDIT_GROUP',                     'Editare grup');
    define ('CANNOT_DELETE_MAPPED_GROUPS',          'Nu se poate sterge grupul care indica o anumita zona! Ne pare rau!');
    define ('CANNOT_DELETE_MAPPED_USERS',           'Nu se poate sterge utilizatorul care indica o anumita zona! Ne pare rau!');
    define ('CANNOT_DELETE_MAPPED_ZONE',            'Nu se poate sterge zona care indica un anumit utilizator sau grup! Ne pare rau!');
    define ('ADMIN_USERNAME_IS_MANDATORY',          'Campul cu numele de utilizator este gol! Acesta este obligatoriu!');
    define ('ADMIN_USER_PASSWORDS_DONT_MATCH',      'Cele 2 parole nu sunt identice!');
    define ('ADMIN_PROFILE_EDIT',                   'Editati profilul utilizatorului');
    define ('ADMIN_PROFILE_ADD',                    'Adaugati profilul utilizatorului');
    define ('ADMIN_PROFILE_USERNAME',               'Utilizator');
    define ('ADMIN_PROFILE_PASSWORD',               'Parola');
    define ('ADMIN_PROFILE_PASSWORD_CONFIRM',       'Confirmati parola');
    define ('ADMIN_PROFILE_EMAIL',                  'E-Mail');
    define ('ADMIN_PROFILE_PHONE',                  'Telefon');
    define ('ADMIN_PROFILE_LAST_NAME',              'Prenume');
    define ('ADMIN_PROFILE_FIRST_NAME',             'Nume');
    define ('ADMIN_PROFILE_COUNTRY',                'Tara');
    define ('ADMIN_PROFILE_GROUP',                  'Grup');
    define ('ADMIN_INVALID_EMAIL',                  'Adresa de e-mail invalida!');
    define ('ADMIN_PROFILE_ACTIVATED',              'Activat');
    define ('ADMIN_PROFILE_ACTIVATED_YES',          'Da');
    define ('ADMIN_PROFILE_ACTIVATED_NO',           'Nu');
    define ('ADMIN_USERNAME_ALREADY_EXISTS',        'Utilizatorul deja exista! Nu poate fi utilizat!');
    define ('ADMIN_EMAIL_ALREADY_EXISTS',           'Adresa de e-mail deja exista! Nu poate fi utilizata!');
    define ('ADMIN_PHONE_TEN_CHARS',                'Numarul de telefon trebuie sa aiba 10 cifre, inclusiv prefix!');
    define ('ADMIN_SEARCH_FIELD_IS_EMPTY',          'Campul de cautare este gol!');
    define ('ADMIN_SEARCH_USER_BY',                 'Cauta dupa');
    define ('ADMIN_SEARCH_USER_IN',                 'Cauta in');
    define ('ADMIN_ZONE_NAME_CANNOT_BE_EMPTY',      'Numele zonei nu poate fi gol! Ne pare rau!');
    define ('ADMIN_ADD_ZONE',                       'Adauga zona');
    define ('ADMIN_EDIT_ZONE',                      'Editeaza zona');
    define ('ADMIN_ZONE_NAME',                      'Zona');
    define ('ADMIN_ZONE_PRICE',                     'Pret');
    define ('ADMIN_ZONE_DESCRIPTION',               'Descriere');
    define ('ADMIN_EDIT_ACL',                       'Editare ACL');
    define ('ADMIN_ADD_ACL',                        'Adaugare ACL');
    define ('ADMIN_ACL_ENTITY',                     'Grup sau utilizator');
    define ('ADMIN_ACL_ACCESS_TYPE',                'Tip de acces');
    define ('ADMIN_ACL_ALLOWED',                    'PERMIS');
    define ('ADMIN_ACL_DENIED',                     'INTERZIS');
    define ('ADMIN_LOG_OUT_TEXT',                   'Iesire');
    define ('ADMIN_PAGE_REGISTER_MESSAGE',          'Continut');
    define ('ADMIN_PAGE_REGISTER_TITLE',            'Titlu');
    define ('ADMIN_PROFILE_SIGNATURE',              'Semnatura');
    define ('ADMIN_PROFILE_DESCRIPTION',            'Descriere');
    define ('ADMIN_PROFILE_YM',                     'YM!');
    define ('ADMIN_PROFILE_MSN',                    'MSN');
    define ('ADMIN_PROFILE_ICQ',                    'ICQ');
    define ('ADMIN_PROFILE_AOL',                    'AOL');
    define ('ADMIN_PROFILE_CITY',                   'Oras');

    # Define messages, that are LONG ...
    define ('AUTHENTICATION_TOOLTIP',               'Buna administrare a utilizatorilor este cheia oricarei aplicatii de succes, 
	pentru ca utilizatorii sunt telul in orice afacere. Ei pot deveni in scurt timp clienti sau utilizatori ai serviciilor prestate 
	si va pot aduce venituri si faima. Modul in care va administrati utilizatorii este modul in care va administrati afacerea si noi 
	va dam posibiliatea unei bune administrari...');

    define ('ADMIN_PAGE_REGISTER_MESSAGE_INFO',     'Pagina unde este solicitat formularul de "Inregistrare" trebuie sa aiba un mesaj 
	care sa explice avantajele date utilizatorilor inregistrati sau cum se foloseste formularul. Acesta a devenit un standard in 
	timp si noi va dam posibilitatea de a edita acel mesaj aici, folosind un editor WYSIWYG...');

    define ('ADMIN_PAGE_REGISTER_OK_MESSAGE_INFO',  'Pagina unde retrimite formularul de inregistrare trebuie sa aiba un mesaj. 
	Puteti edita acest mesaj de aici si va aparea in locul corect! De obicei, pe aceasta pagina puteti informa utilizatorul ca i-ati 
	trimis un e-mail cu informatii sau cu actiunile necesare pentru a isi completa inregistrarea.');

    define ('ADMIN_PAGE_REGISTER_TITLE_INFO',       'Avem nevoie de un titlu care va fi aratat si folosit in Etichetele <title></title> 
	ale paginii care face apel la formularul de inregistrare. Conceptul unui formular de inregistrare inseamna ca acesta are de obicei 
	o pagina separata in contextul website-ului, deoarece are un singur scop: de a permite accesul facil al utilizatorului catre un 
	mecanism simplu de creare a contului. De aceea va permitem sa editati titlul acelei pagini...');

    define ('ADMIN_TOOLTIP_LOGIN_USERNAME',         'Daca intr-adevar sunteti administratorul, ne asteptam sa introduceti utilizatorul 
	corect aici. Nu va faceti probleme si nu incercati sa ne hack-uiti, deoarece inregistram fiecare incercare de autentificare 
	esuata, impreuna cu alte date, cum ar fi IP-ul, browserul, sistemul de operare si, in linii mari, tot...');

    define ('ADMIN_TOOLTIP_LOGIN_PASSWORD',         'Ar trebui sa introduceti parola corecta aici. Daca totul este in regula, ar trebui 
	sa fiti dusi catre urmatorul ecran de administrare. Daca autentificarea nu este corecta, va vom arata acest formular din nou si, 
	daca vrem, va vom da un indiciu... sau NU...');

    define ('ADMIN_TOOLTIP_MANAGE_USERS',           'Administrarea utilizatorilor este poate conceptul oricarei aplicatii bazate pe Web. 
	Fara utilizatori, internetul ar fi mort, Bill Gates ar fi mort si viata nu ar fi asa frumoasa. Pentru a evita un viitorul de genul, 
	va dam posibilitatea de a crea, edita si sterge utilizatori din sistemul sau aplicatia dumneavoastra...');

    define ('ADMIN_TOOLTIP_MANAGE_GROUPS',          'Utilizatorii sunt origanizati in grupuri. De ce? Pentru ca trebuie sa permitem 
	unor grupuri masive de utilizatori acces la unele parti ale aplicatiei. Pentru asta, utilizatorii pot face parte dintr-un grup si 
	daca din intamplare au nevoie de mai mult acces, va dam posibilitatea de a le da acest lucru cu ajutorul sistemului de ACL-uri...');

    define ('ADMIN_TOOLTIP_MANAGE_ZONES',           'Zonele sunt un concept care poate fi inteles usor. Sunt zone delimitate ale aplicatiei 
	pe care doar utilizatorii autentificati cu anumite permisiuni le pot accesa sau pot vedea continutul lor.');

    define ('ADMIN_TOOLTIP_MANAGE_ACLS',            'Aici puteti vedea maparea intre grupurile de utilizatori si utilizatori, cu zone 
	specifice. Daca vreti sa permiteti sau sa interziceti grupurilor de utilizatori sau utilizatorilor, aici este locul unde puteti 
	edita acele mapari....');

    define ('ADMIN_TOOLTIP_MANAGE_CONFIG',          'Editati modul in care utilizatorii se autentifica sau ce campuri sunt aratate 
	pe pagina lor de profil sau orice alta setare legata de utilizatori sau grupuri. Administrarea modulului de autentificare are nevoie 
	de posibilitatea de a edita anumite setari sau optiuni...');

    define ('ADMIN_CATEGORY_MOVED',                 'Trebuie sa stim cum vreti sa faceti aceasta mutare, ca una dintre cele 4 mutari 
	posibile pe care puteti sa le faceti. Daca de exemplu faceti o mutare care nu este valida, o vom ignora. Avertisment: daca 
	mutati o ramura, va muta toata ramura, nu doar nodul...');
?>