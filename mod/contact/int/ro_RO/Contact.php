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
    define ('CONTACT_DASHBOARD',                    'Contact');
    define ('CONTACT_MANAGE_MESSAGES',              'Mesaje');
    define ('CONTACT_MANAGE_SUBJECTS',              'Subiecte');
    define ('CONTACT_MANAGE_CONFIG',                'Configuratie');
    define ('CONTACT_ADD_SUBJECT',                  'Adaugare subiect contact');
    define ('CONTACT_EDIT_SUBJECT',                 'Editare subiect contact');
    define ('CONTACT_SUBJECT',                      'Subiect');
    define ('CONTACT_MANAGE_CONFIGURATION_UPDATE',  'Actualizare setari');
    define ('CONTACT_EMAIL',                        'EMail');
    define ('CONTACT_PAGE_TITLE',                   'Titlu');
    define ('CONTACT_PAGE_CONTENT',                 'Continut');
    define ('CONTACT_PAGE_TITLE_OK',                'Titlu');
    define ('CONTACT_PAGE_CONTENT_OK',              'Continut');
    define ('CONTACT_SUBJECT_CANNOT_BE_EMPTY',      'Titlul subiectului nu poate fi gol!');
    define ('CONTACT_URL_TO_REDIRECT_NOT_SET',      'Nu ati setat un URL de redirectionare pentru contact!');
    define ('CONTACT_URL_TO_REDIRECT_NOT_SET_FIX',  'Va rugam setati un URL corect inainte de a apela la formularul de contact.');
    define ('CONTACT_VIEW_FROM',                    'De la: ');
    define ('CONTACT_VIEW_SUBJECT',                 'Subiect: ');
    define ('CONTACT_VIEW_MESSAGE',                 'Mesaj: ');
    define ('CONTACT_VIEW_COMMENT',                 'Comentariu: ');
    define ('CONTACT_VIEW_RESOLVED',                'Reszolvat: ');
    define ('CONTACT_RESEND_MESSAGE',               'Retrimite mesajul de contact');
    define ('CONTACT_EDIT_COMMENT_AND_STATUS',      'Actualizati detaliile mesajului');
    define ('CONTACT_MESSAGE_RESOLVED',             'Rezolvat');
    define ('CONTACT_MESSAGE_RESOLVED_YES',         'Da');
    define ('CONTACT_MESSAGE_RESOLVED_NO',          'Nu');
    define ('CONTACT_MESSAGE_COMMENT',              'Comentariu');
    define ('CONTACT_MESSAGES_DEFINED_FOR_SUBJECT', 'Nu pot sterge subiectul atata timp cat sunt mesaje definite pentru el!');
    define ('CONTACT_CONFIG_SUCCES_PAGE',           'Editeaza continutul pe pagina de SUCCES a formularului de contact...');
    define ('CONTACT_CONFIG_SUCCES_PAGE_TITLE',     'Editeaza titlul pe pagina de SUCCES a formularului de contact...');
    define ('CONTACT_CONFIG_PAGE',                  'Editeaza continutul de pe pagina formularului de contact...');
    define ('CONTACT_CONFIG_PAGE_TITLE',            'Editeaza titlul de pe pagina formularului de contact...');
    define ('CONTACT_CONFIG_EMAIL',                 'Editeaza e-mail-ul la care sunt trimise mesajele de contact...');
    define ('CONTACT_CONFIG_CHOOSE',                'Alege');
    define ('CONTACT_CONFIG_DO',                    'Executa');
    define ('CONTACT_STATUS_URL',                   'Status');

    # Define messages, that are LONG ...
    define ('CONTACT_TOOLTIP',                      'Mesajele care sunt primite prin formularul de contact sunt salvate aici, astfel 
	incat sa le puteti vedea si sa le stergeti dupa. Salvam mesajele de contact pentru ca, pe langa sa le trimitem la o adresa 
	de e-mail configurata, este o sansa de a le sterge din greseala sau nu le primiti. Astfel, sa aveti un back-up este o optiune 
	buna. Folositi aces modul pentru a configura modul in care sunteti contactati prin intermediul aplicatiei...');

    define ('CONTACT_EMAIL_INFO',                   'Avem nevoie de un e-mail unde vom trimite toate mesajele primite. Nu verificam 
	daca acesta este un e-mail valid, deoarece tine de dumneavoastra. Orice mesaj de contact primit prin intermediul formularului 
	de contact va fi trimis catre acest e-mail. Este in interesul dumneavoastra sa introduceti un e-mail corect aici!');

    define ('CONTACT_PAGE_TITLE_INFO',              'Pagina de contact este aproape mereu separata de restul website-ului. Din cauza 
	acestui statut special, va dam posibilitatea de a edita titlul paginii de contact, astfel incat, de exemplu, puteti seta anumite 
	cuvinte cheie in titlul paginii, sau sa dati informatii mai bune utilizatorilor in legatura cu functionalitatile prezente pe pagina...');

    define ('CONTACT_PAGE_CONTENT_INFO',            'Pe pagina de contact, va permitem sa setati un mesaj. Acest mesaj poate fi editat 
	la randul lui. In acest mesaj puteti da informatii utilizatorilor in legatura cu folosirea formularului de contact sau cat de 
	repede vor primi un raspuns la mesajul lor. Aici puteti edita acel mesaj, folosind un editor WYSIWYG...');

    define ('CONTACT_PAGE_TITLE_OK_INFO',           'Daca formularul de contact este valid, trimitem utilizatorul catre o pagina 
	specifica. Pe aceasta pagina specifica, avem nevoie de un titlu si un continut. Puteti edita titlul paginii aici, urmand sa fie 
	folosit in orice loc este apelat...');

    define ('CONTACT_PAGE_CONTENT_OK_INFO',         'Va permitem, folosind un editor WYSIWYG, sa editati continutul paginii catre 
	care redirectionam utilizatorul dupa ce formularul de contact se valideaza. Acest lucru este facut pentru a va permite sa setati 
	un mesaj care va fi aratat pe acea pagina. Puteti scrie orice mesaj aici, nu sunteti limitati de nimic...');

    define ('CONTACT_TOOLTIP_MANAGE_MESSAGES',      'Fiecare mesaj primit prin intermediul formularului de contact este in primul 
	rand trimis prin e-mail la o adresa configurata in sectiunea dedicata, dar si pastrat in baza de date. Le pastram in baza de 
	date, deoarece exista sansa ca e-mail-ul sa nu va fie livrat...');

    define ('CONTACT_TOOLTIP_MANAGE_SUBJECTS',      'De obicei oamenii va contacteaza pentru un subiect anume. Nu le dam posibilitatea 
	oamenilor sa isi seteze subiectele proprii, ci va dam dumneavoastra aceasta posibilitate. De ce? Pentru administrare, deoarece 
	puteti, de exemplu, crea filtre bazate pe subiect in clientul dumneavoastra de e-mail sau puteti, pur si simplu, sorta mesajele dupa subiect...');

    define ('CONTACT_TOOLTIP_MANAGE_CONFIG',        'Va permitem sa setati parametrii de configurare necesari astfel incat sa puteti 
	primi mesaje de contact sau sa limitati numarul de mesaje de contact pe care le primiti. Optiunile legate de modulul de contact 
	pot fi configurate aici...');
?>