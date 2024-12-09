use usjr;

CREATE TABLE `usjr`.`appusers`(
	`uid`  int not null auto_increment,
    `name`  varchar(45) not null,
    `password` varchar(255) not null,
	PRIMARY Key (`uid`)
); 
select * from `usjr`.`appusers`;

drop table `usjr`.`appusers`;
ALTER TABLE `usjr`.`appusers` MODIFY password VARCHAR(255);
