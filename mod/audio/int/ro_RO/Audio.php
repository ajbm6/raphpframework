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
    define ('ARTICLE_MENU',                        'Articole');
    define ('ARTICLE_MANAGE_DASHBOARD',                     'Panou de control');
    define ('ARTICLE_MANAGE_ARTICLES',                      'Articole');
    define ('ARTICLE_MANAGE_CATEGORIES',                    'Categorii');
    define ('ARTICLE_MANAGE_ARTICLES_MOVE',                 'Operatiuni');
    define ('ARTICLE_MANAGE_ARTICLES_CONFIG',               'Configuratie');
    define ('ARTICLE_CATEGORY_NAME_IS_EMPTY',        'Numele categoriei nu poate fi gol!');
    define ('ARTICLE_CATEGORY_ALREADY_EXISTS',              'Categoria deja exista!');
    define ('ARTICLE_CATEGORY_LAST_CHILD',          'ultima subcategorie');
    define ('ARTICLE_CATEGORY_CHILD',               'prima subcategorie');
    define ('ARTICLE_CATEGORY_BROTHER',             'categoria precedenta');
    define ('ARTICLE_CATEGORY_NEXT_BROTHER',        'categoria urmatoare');
    define ('ARTICLE_CATEGORY_MOVED_TO_CHILD',      'Nu poti muta aceasta categorie intr-o subcategorie a acesteia! Este ilegal sa faci acest lucru!');
    define ('ARTICLE_OF_CATEGORY',                  'Al categoriei');
    define ('ARTICLE_AS_A',                         'Ca o');
    define ('ARTICLE_CATEGORY_NAME_LABEL',          'Categorie');
    define ('ARTICLE_CATEGORY_DESCRIPTION',         'Descriere');
    define ('ARTICLE_STATE',                        'Stare');
    define ('ARTICLE_DRAFT',                        'Proiect');
    define ('ARTICLE_PUBLISHED',                    'Publicat');
    define ('ARTICLE_PENDING_REVIEW',               'In asteptare');
    define ('ARTICLE_STICKY',                       'Lipire');
    define ('ARTICLE_ADD_CATEGORY',                 'Adauga categorie');
    define ('ARTICLE_EDIT_CATEGORY',                'Editeaza categorie');
    define ('ARTICLE_MOVE_ARTICLE',                 'Muta articole');
    define ('ARTICLE_OLD_CATEGORY',                 'Categorie veche');
    define ('ARTICLE_NEW_CATEGORY',                 'Categorie noua');
    define ('ARTICLE_TITLE_CANNOT_BE_EMPTY',        'Titlul articolului nu poate fi gol!');
    define ('ARTICLE_CONTENT_CANNOT_BE_EMPTY',      'Continutul articolului nu poate fi gol!');
    define ('ARTICLE_TITLE_MUST_BE_UNIQUE',         'Titlul articolului trebuie sa fie unic!');
    define ('ARTICLE_URL_MUST_BE_UNIQUE',           'URL-ul auto generat nu este unic. Schimbati usor titlul.');
    define ('ARTICLE_CATEGORY_URL_MUST_BE_UNIQUE',  'URL-ul auto generat nu este unic. Schimbati usor numele');
    define ('ARTICLE_ADD_ARTICLE',                  'Adauga articol');
    define ('ARTICLE_EDIT_ARTICLE',                 'Editeaza articol');
    define ('ARTICLE_UPDATE_CONFIGURATION',         'Actualizare setari');
    define ('ARTICLE_TITLE',                        'Titlu');
    define ('ARTICLE_TAGS',                         'Etichete');
    define ('ARTICLE_CONTENT',                      'Continut');
    define ('ARTICLE_EXCERPT',                      'Fragment');
    define ('ARTICLE_AUTHOR',                       'Autor');
    define ('ARTICLE_CATEGORY_HAS_ARTICLES',        'Nu se poate sterge categoria care contine articole!');
    define ('ARTICLE_DEFAULT_CATEGORY',             'Categorie preexistenta');
    define ('ARTICLE_CONFIG_CHOOSE',                'Alegeti');
    define ('ARTICLE_CONFIG_DATE_FORMAT',           'Editati formatul datei cand se afiseaza articolele ...');
    define ('ARTICLE_CONFIG_ARTICLES_PER_PAGE',     'Editati numarul de articole care se vor afisa pe pagina (de obicei 10)...');
    define ('ARTICLE_CONFIG_PER_PAGE_ERROR',        'Ati atasat un numar care nu este valid. Incercati inca o data ...');
    define ('ARTICLE_SEARCH_BY',                    'Cautare');
    define ('ARTICLE_SEARCH_IN',                    'In');
    define ('ARTICLE_SEARCH_TITLE',                 'Titlu');
    define ('ARTICLE_SEARCH_CATEGORY',              'Categorie');
    define ('ARTICLE_SEARCH_CONTENT',               'Continut');
    define ('ARTICLE_SEARCH_TAGS',                  'Etichete');
    define ('ARTICLE_SEARCH_EXCERPT',               'Fragment');
    define ('ARTICLE_URL',                          'Articol');
    define ('ARTICLE_DATE_PUBLISHED',               'Publicat: ');
    define ('ARTICLE_DATE_UPDATED',                 'Actualizat: ');
    define ('ARTICLE_CATEGORY',                     'Categorie: ');
    define ('ARTICLE_URL_NOT_SET',                  'URL-ul pentru articole nu a fost setat!');
    define ('ARTICLE_URL_NOT_SET_FIX',              'Este recomandat verificarea codului ...');
    define ('ARTICLE_FIRST_ARTICLE',                'Nici o intrare precedenta ...');
    define ('ARTICLE_LAST_ARTICLE',                 'Nici o intrare urmatoare ...');

    # Define messages, that are LONG ...
    define ('ARTICLE_TOOLTIP',                      'Va dam posibilitatea de a crea, edita si sterge artcole, sa administrati comentarii
	si sa mentineti o sectiune tip blog sau stiri in aplicatia web.	Puteti folosi functionalitatile de aici pentru a face treaba
	pentru dumneavoastra. Articolele vor putea fi organizate in categorii si sub-categorii nelimitate care sa va dea putere de administrare nelimitata...');

    define ('ARTICLE_DEFAULT_CATEGORY_MESSAGE',     'Cand nu se definesc categorii speciale, este creata una default. Setati numele
	categoriei default aici, si daca nu sunt definite categoriile in baza de date, prima categorie care va fi creata va utiliza
	acest nume! Acest nume de categorie nu ar trebui lasat gol...');

    define ('ARTICLE_TOOLTIP_MANAGE_ARTICLES',      'Aici puteti vedea ce articole au fost scrise de utilizatori, puteti edita
	sau sterge acele articole, modifica permisiunile pentru ele sau sa le editati continutul');

    define ('ARTICLE_TOOLTIP_MANAGE_CATEGORIES',    'Pentru sortare si organizare, articolele sunt stocate in categorii, si pentru
	acel scop va punem la dispozitie uneltele de administrare pentru a crea, edita sau sterge categorii si pentru a le organiza
	in ierarhii diferite...');

    define ('ARTICLE_TOOLTIP_MANAGE_OPERATIONS',    'Pentru un management masiv, va dam posibiltatea de a face asemenea operatii.
	Sunt anumite momente cand veti vrea sa mutati mai multe articole dintr-o categorie in alta. Pentru acest scop, va dam uneltele
	cu care puteti lucra...For basic massive management, we give you the tools to do such operations.');

    define ('ARTICLE_TOOLTIP_MANAGE_CONFIG',        'Definiti care articole sa fie afisate pe pagina sau definiti categoria default
	care sa fie creata sau in ce categorie sa fie salvate articolele Uncategorized. Putere nelimitata. Doar pentru dumneavoastra...');

    define ('ARTICLE_CATEGORY_MOVED',               'Trebuie sa stim cum vreti sa faceti aceasta mutare, ca una dintre cele 4 mutari
	posibile pe care puteti sa le faceti. Daca de exemplu faceti o mutare care nu este valida, o vom ignora. Avertisment: daca
	mutati o ramura, va muta toata ramura, nu doar nodul...');

    define ('ARTICLE_TAGS_INFO',                    'Va permitem sa setati taguri, altfel cunoscute ca si cuvinte cheie pentru
	fiecare articol pe care il scrieti. Aceste cuvinte cheie vor fi folosite in cuvintele cheie meta si in descrierea meta, pentru
	SEO. Pentru a va permite administrarte totala asupra tehnicilor SEO, va dam posibilitatea de a seta cuvintele cheie manual.
	Pentru cele mai bune rezultate, introduceti o lista de cuvinte cheie separate prin virgula care sunt legate de continutul articolului.');
?>