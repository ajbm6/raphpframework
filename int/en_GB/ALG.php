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
    define ('MPTT_UNCOMPATIBLE_TABLE',              'Table is MPTT incompatible. Cannot build MPTT object!'                     );
    define ('MPTT_UNCOMPATIBLE_TABLE_FIX',          'Make sure the table is MPTT compatible.'                                   );
    define ('MPTT_NODE_EXISTS',                     'Requested NODE already exists.'                                            );
    define ('MPTT_NODE_EXISTS_FIX',                 'Due to BTREE searches we only allow UNIQUE indexes.'                       );
    define ('MPTT_SELF_REFERENCE',                  'The current node, references itself.'                                      );
    define ('MPTT_SELF_REFERENCE_FIX',              'Node cannot be moved/copied against itself.'                               );
?>