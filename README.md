Staff picks
====


## Install Notes
Create the DB. If you're using MySQL, you might do something like this:
    create database staff_picks character set utf8;
    grant all on staff_picks.* to staff_picks@'localhost' identified by 'staff_picks';
    CREATE TABLE item (id INT AUTO_INCREMENT PRIMARY KEY, title varchar(1000), hollis varchar(20), isbn varchar(20), selected_by varchar(1000), cover_path varchar(1000), picked TIMESTAMP DEFAULT CURRENT_TIMESTAMP);