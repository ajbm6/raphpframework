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
    define ('MANAGE_PRODUCTS',                      'Produse');
    define ('MANAGE_PRODUCT_CATEGORIES',            'Categorii');
    define ('MANAGE_PRODUCT_OPERATIONS',            'Operatii');
    define ('MANAGE_PRODUCT_CONFIGURATION',         'Configuratie');
    define ('PRODUCTS_CODE_MUST_BE_UNIQUE',         'impunem codul produsului sa fie unic. Schimbati-l ...');
    define ('PRODUCTS_NAME_MUST_BE_UNIQUE',         'impunem numele produsului sa fie unic. Schimbati-l ...');
    define ('PRODUCTS_STOCK_MUST_BE_ENTERED',       'Este nevoie sa introduceti stocul acestui produs ...');
    define ('PRODUCTS_PRICE_MUST_BE_ENTERED',       'Este nevoie sa introduceti pretul acestui produs ...');
    define ('PRODUCTS_CODE_MUST_BE_ENTERED',        'Este nevoie sa introduceti codul produsului. Campul pentru cod nu trebuie sa fie gol!');
    define ('PRODUCTS_NAME_MUST_BE_ENTERED',        'Este necesar sa introduceti un nume de produs. Campul pentru nume nu poate ramane gol!');
    define ('PRODUCTS_URL_MUST_BE_UNIQUE',          'URL-ul autogenerat nu este unic. Chimbati usor numele produsului...');
    define ('PRODUCTS_STOCK_CANNOT_BE_NEGATIVE',    'Stocul poate fi pe ZERO sau cu valoare pozitiva. Negativele nu sunt permise !');
    define ('PRODUCTS_PRICE_CANNOT_BE_NEGATIVE',    'Pretul poate fi Pe zero sau cu valoare pozitiva. Negativele nu sunt permise!');
    define ('PRODUCTS_CANNOT_DELETE_CATEGORY',      'Categoria nu poate fi stearsa intrucat sunt marcate produse!');
    define ('PRODUCT_CATEGORY_CANNOT_BE_MOVED',     'Categoria nu poate fi mutata la o subcategorie a acesteia ...');
    define ('PRODUCTS_CATEGORY_NAME_EMPTY',         'Numele categoriei nu poate fi gol!');
    define ('PRODUCTS_CATEGORY_ALREADY_EXISTS',     'Categoria exista deja!');
    define ('PRODUCTS_ADD_PRODUCT',                 'Adauga produs');
    define ('PRODUCTS_EDIT_PRODUCT',                'Editeaza produs');
    define ('PRODUCTS_EDIT_CATEGORY',               'Editeaza categorie');
    define ('PRODUCTS_CATEGORY',                    'Categorie');
    define ('PRODUCTS_CODE',                        'Cod');
    define ('PRODUCTS_NAME',                        'Nume');
    define ('PRODUCTS_URL',                         'URL');
    define ('PRODUCTS_STOCK',                       'Stoc');
    define ('PRODUCTS_PRICE',                       'Pret');
    define ('PRODUCTS_PDF',                         'PDF');
    define ('PRODUCTS_DECRIPTION',                  'Descriere');
    define ('PRODUCTS_ACTION_IMAGE',                'Fara imagine');
    define ('PRODUCTS_ACTION_PROPERTY',             'Executa propietate');
    define ('PRODUCTS_PROPERTY_CANNOT_BE_EMPTY',    'Propietatea nu poate fi goala!');
    define ('PRODUCTS_VALUE_CANNOT_BE_EMPTY',       'Valuarea acestei propietati nu poate fi goala!');
    define ('PRODUCTS_ADD_PROPERTY',                'Adauga propietate');
    define ('PRODUCTS_PROPERTY',                    'Propietate');
    define ('PRODUCTS_VALUE',                       'Valuare');
    define ('PRODUCTS_EDIT_PROPERTY',               'Editeaza propietate');
    define ('PRODUCTS_IMAGE_TITLE_CANNOT_BE_EMPTY', 'Titlul acestei imagini nu poate fi gol!');
    define ('PRODUCTS_ADD_IMAGE',                   'Adauga imagine');
    define ('PRODUCTS_IMAGE_TITLE',                 'Titlu');
    define ('PRODUCTS_IMAGE',                       'Imagine');
    define ('PRODUCTS_CAPTION',                     'Subtitlu');
    define ('PRODUCTS_EDIT_IMAGE',                  'Editeaza imagine');
    define ('PRODUCTS_MOVE_PRODUCTS',               'Muta produs');
    define ('PRODUCTS_OLD_CATEGORY',                'Categorie veche');
    define ('PRODUCTS_NEW_CATEGORY',                'Categorie noua');
    define ('PRODUCTS_MANAGE_CONFIGURATION',        'Actualizare configuratie');
    define ('PRODUCTS_CONFIG_CHOOSE',               'Alege');

    // Define messages, that are LONG ...
    define ('MANAGE_PRODUCTS_TOOLTIP',              'Vinde, vinde, vinde. Este ceea ce iti produce venit, ceea ce iti aduce venit si face ca afacerea 
    sa mearga. iti punem la dispozitie uneltele care iti vor aduce venit. De asemenea iti dam voie sa configurezi modalitatea prin care ultilizatorii 
    platesc produsele cumparate!');
    
    define ('MANAGE_PRODUCTS_MP_TOOLTIP',           'va oferim lista tuturor produselor definite pe sistem. Pot fi sortate pe categorii,
    sau in orice alt mod. Pentru fiecare in parte veti putea sorta poze (imagini) si sa specificati caracteristici in dreprul oricarui produs');
    
    define ('MANAGE_CATEGORIES_TOOLTIP',            'Produsele sunt organizate pe categorii, pentru o mai usoara vizualizare si administrare. Pentru
    o navigare mai buna a utilizatorilor, este nevoie sa iti oganizezi produsele astfel incat sa le fie mai usor utilizatorilor de a gasi mai usor 
    ceea ce cauta printre ramurile categoriilor.');
    
    define ('MANAGE_OPERATIONS_TOOLTIP',            'Uneori este nevoie de mutarea produselor dintr-o categorie in alta. Cand ai mii de produse, mutarea
    individuala a fiecarui produs este o munca pe care nimeni nu vrea sa o faca. De aceea iti oferim ustensilele de automatizare a acestui lucru astfel 
    incat produsele vor fi mutate in masa de la o categorie la alta sau prin alta operatiune in masa.');
   
    define ('MANAGE_CONFIG_TOOLTIP',         		'Exista obtiuni specifice de configuratie pe care vrei sa o aplici produselor tale .
    De exemplu poti seta sa vizualizezi un stoc, sa inregistrezi utilizatori inainte ca acestia sa fi facut comanda pe un anumit produs sau orice alt
    gen de configuratie permanenta.');
?>