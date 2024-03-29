-- @copyright 2015-2022 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
create table people (
	id                   int unsigned not null primary key auto_increment,
	firstname            varchar(128) not null,
	lastname             varchar(128) not null,
	email                varchar(255) not null,
	username             varchar(40) unique,
	password             varchar(40),
	authenticationMethod varchar(40),
	role                 varchar(30)
);

create table aggregations (
    id                 int unsigned not null primary key auto_increment,
    name               varchar(128) not null,
    google_calendar_id varchar(128) not null
);

create table aggregatedCalendars (
    id                 int unsigned not null primary key auto_increment,
    aggregation_id     int unsigned not null,
    name               varchar(128) not null,
    google_calendar_id varchar(128) not null,
    foreign key (aggregation_id) references aggregations(id)
);
