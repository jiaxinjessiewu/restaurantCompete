DROP TABLE IF EXISTS appuser CASCADE;
DROP TABLE IF EXISTS voterecord CASCADE;
DROP TABLE IF EXISTS work CASCADE;
create table appuser (
	id varchar(50) primary key,
	password varchar(50)
);

insert into appuser values('auser', 'apassword');

create table work(
	name text Primary Key,
	rate double precision DEFAULT 1000
);	

create table voterecord (
	id varchar(50) REFERENCES appuser(id) ON UPDATE CASCADE,
	field1 text,
	field2 text,
	Primary Key(id,field1,field2)
);




