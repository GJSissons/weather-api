db_name storyt22_weather

db_user storyt22_wp802

db_pass *****

db_host localhost


create table weather_history (
    datekey varchar(255),
    location varchar(255),
    unitgroup varchar(255),
    json text(32768),
    time_created timestamp,
    CONSTRAINT uc_datelocation UNIQUE (datekey,location)
);


drop table weather_history;

delete FROM `weather_history` WHERE 1;
