What needs to be created when I have the db up and running:
_______________________________________________________________
* tables called:
    * tournament_name
        * columns: (id, name, active)
    * schools
        * columns: (id, school_name, school_abbrv, pwd)
            * have one row with values (1, HC_School, HCS, /someCode/)
    * activity
        * columns: (id, type, active) 
            * have one row with values (1, tournament_exists, 0)
            * have another row with values(1, tournament_started, 0)


ALSO: need to change the db.ini file to represent what the actual db info is
_______________________________________________________________



// to check errors in php:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


11/15/2024:
next step:
make it so you can only DELETE values in your (own coach) table.
make it so that values aren't added to all_wrestlers until after the coaches press SUBMIT WRESTLERS on their data. after they submit these wrestlers, they cannot be added.
create alerts that say deletion cannot be undone(for schools), and also that info can't be changed when creating a tournament