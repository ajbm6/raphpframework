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
    define ('TEXTS_MENU',                           'Texte');
    define ('MANAGE_TEXTS',                         'Texte');
    define ('MANAGE_TEXTS_CATEGORIES',              'Categorii');
    define ('MANAGE_TEXTS_MOVE',                    'Operatiuni');
    define ('MANAGE_TEXTS_CONFIG',                  'Configuratie');
    define ('TEXTS_CATEGORY_NAME_CANNOT_BE_EMPTY',  'Numele categoriei nu poate fi gol!');
    define ('TEXTS_CATEGORY_ALREADY_EXISTS',        'Categoria deja exista!');
    define ('TEXTS_CATEGORY_LAST_CHILD',            'ultima subcategorie');
    define ('TEXTS_CATEGORY_CHILD',                 'prima subcategorie');
    define ('TEXTS_CATEGORY_BROTHER',               'categoria precedenta');
    define ('TEXTS_CATEGORY_NEXT_BROTHER',          'categoria urmatoare');
    define ('TEXTS_CATEGORY_MOVED_TO_CHILD',        'Nu se poate muta categoria intr-o subcategorie a ei! Este ilegal sa faci asta!');
    define ('TEXTS_OF_CATEGORY',                    'Din categoria');
    define ('TEXTS_AS_A',                           'Ca si');
    define ('TEXTS_CATEGORY_NAME_LABEL',            'Categorie');
    define ('TEXTS_ADD_CATEGORY',                   'Adaugare categorie');
    define ('TEXTS_EDIT_CATEGORY',                  'Editare categorie');
    define ('TEXTS_MOVE_ARTICLE',                   'Mutare texte');
    define ('TEXTS_OLD_CATEGORY',                   'Categorie veche');
    define ('TEXTS_NEW_CATEGORY',                   'Categorie noua');
    define ('TEXTS_TITLE_CANNOT_BE_EMPTY',          'Identificatorul de text nu poate fi gol!');
    define ('TEXTS_CONTENT_CANNOT_BE_EMPTY',        'Continutul nu poate fi gol!');
    define ('TEXTS_ADD_ARTICLE',                    'Adaugare text');
    define ('TEXTS_EDIT_ARTICLE',                   'Editare text');
    define ('TEXTS_UPDATE_CONFIGURATION',           'Update setari');
    define ('TEXTS_TITLE',                          'Identificator text');
    define ('TEXTS_CONTENT',                        'Continut');
    define ('TEXTS_TAGS',                           'Etichete');
    define ('TEXTS_AUTHOR',                         'Autor');
    define ('TEXTS_CANNOT_DELETE_CATEGORY_WA',      'Nu se poate sterge o categorie care contine articole!');
    define ('TEXTS_DEFAULT_CATEGORY',               'Default');
    define ('TEXTS_CONFIG_DEFAULT_CATEGORY',        'Editati numele categoriei predefinite care va fi creata in cazul in care nu exista alte categorii...');
    define ('TEXTS_CONFIG_CHOOSE',                  'Alegeti');
    define ('TEXTS_CONFIG_DO',                      'Executa');

    # Define messages, that are LONG ...
    define ('TEXTS_TOOLTIP',                        'Textele sunt bucati independente de continut din paginile dumneavoastra, care nu 
	intra in categoria articole, ci mai degraba pagini. Ele pot fi editate aici si afisate oriunde aplicata este programata sa le 
	afiseze. Aceasta este o zona foarte sensibila de administrare unde identificatorii de text nu ar trebui modificati fara schimbari in cod...');

    define ('TEXTS_DEFAULT_CATEGORY_MESSAGE',       'Cand nu este definita nicio categorie, se creeaza una default. Setati numele 
	categoriei default aici si daca nu sunt categorii definite in baza de date, prima categorie care va fi creata va lua acest nume! 
	Acest nume de categorie nu ar trebui lasat gol...');

    define ('TEXTS_TOOLTIP_MANAGE_TEXTS',           'Textele sunt mici bucati de text care sunt afisate in locuri specifice sau 
	aleatoare in aplicatia dumneavoastra, depinzand de structura aplicatiei. Conceptual, sunt diferite de articole, deoarece apar 
	pe o pagina specifica sau pe o categorie specifica de pagini...');

    define ('TEXTS_TOOLTIP_MANAGE_CATEGORIES',      'Pentru o organizare mai buna, textele sunt organizate in categorii, care variaza 
	de la text-boxes, la widgets sau la pagini intregi. Textele sunt strans legate de structura website-ului, insemnand ca vor fi 
	disponibile pentru editare, dar vor fi apelate specific pentru a fi afisate...');

    define ('TEXTS_TOOLTIP_MANAGE_OPERATIONS',      'Administrarea textelor se face la fel ca administrarea articolelor, avand 
	posibilitatea de a face operatiuni in masa. Va dam uneltele pentru ceea ce aveti nevoie...');

    define ('TEXTS_TOOLTIP_MANAGE_CONFIG',          'Modul in care textele sunt afisate sau modul in care functioneaza poate fi 
	configurat din aceasta sectiune. Va permitem sa setati categoria default si sa setati orice alt tip de optiune de configuratie disponibila!');

    define ('TEXTS_CATEGORY_MOVED',                 'Trebuie sa stim cum vreti sa faceti aceasta mutare, ca una dintre cele 4 mutari 
	posibile pe care puteti sa le faceti. Daca de exemplu faceti o mutare care nu este valida, o vom ignora. Avertisment: daca 
	mutati o ramura, va muta toata ramura, nu doar nodul...');

    define ('TEXTS_LOREM_IPSUM_DOLOR_SIT_AMET',     'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisis'      .
    'interdum ipsum. Suspendisse potenti. Curabitur nec leo eget nisi accumsan euismod.');

    define ('TEXTS_TAGS_INFO',                      'Va permitem sa setati taguri, altfel cunoscute ca si cuvinte cheie pentru 
	fiecare articol pe care il scrieti. Aceste cuvinte cheie vor fi folosite in cuvintele cheie meta si in descrierea meta, pentru 
	SEO. Pentru a va permite administrarte totala asupra tehnicilor SEO, va dam posibilitatea de a seta cuvintele cheie manual. 
	Pentru cele mai bune rezultate, introduceti o lista de cuvinte cheie separate prin virgula care sunt legate de continutul textului.');
?>