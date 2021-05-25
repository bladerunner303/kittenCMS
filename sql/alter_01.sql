create table sys_users(
  user_name           varchar(50)   not null ,
  user_pwd            varchar(64)  not null,
  last_login          timestamp     null,
  last_pwd_change     timestamp     null,
  status              varchar(30)   not null,
  role                varchar(30)   not null,
  modifier	          varchar(50)   not null,
  modified	          timestamp     not null default current_timestamp,
constraint pk_sys_users primary key (user_name)
)
engine=innodb;

create table sys_session
(
  id 						      varchar(36) not null,
	ip 						      varchar(40) null,
	browser_hash 			  varchar(64) not null,
	user_name 				  varchar(50) not null,
  user_role           varchar(30) not null,
	login_time 				  timestamp null ,
	last_activity 			timestamp null  ,
	logout_time 			  timestamp null,
  session_data        varchar(2000),
	constraint pk_session primary key (id)
)
ENGINE=InnoDB;

create table bad_logins(
user_name             varchar(50) not null,
created               timestamp   not null
)
engine=innodb;

create table config_settings(
setting_key	          varchar(50)   not null,
setting_value 	      varchar(4000) null,
setting_description   varchar(4000) null,
setting_type          varchar(50)   not null, -- TEXT, TEXTAREA, BOOLEAN
modifier	            varchar(50)   not null,
modified	            timestamp     not null,
constraint pk_config_settigns primary key (setting_key)
)
engine=innodb;

create table config_menu(
id		                varchar(50)   not null,
name		              varchar(50)   null,
menu_type             varchar(50)   not null, -- ALAP, NEWS
tooltip		            varchar(255)  null,
content		            varchar(4000) null,
css                   varchar(4000) null,
js                    varchar(4000) null,
enabled_editor        integer       not null,
visible		            integer       not null,
order_field	          integer       not null,
default_page          integer       not null,
modifier	            varchar(50)   not null,
modified	            timestamp     not null,
deleted_by            varchar(50)   null,
deleted               timestamp     null,
constraint pk_config_menu primary key (id)
)
engine=innodb;

create table news(
  id		                varchar(36)   not null,
  menu_id               varchar(50)   not null,
  title                 varchar(50)   null,
  content               varchar(4000) null,
  visible               integer       not null,
  highlight             integer       not null,
  creator               varchar(50)   not null,
  created               timestamp     null,
  modifier              varchar(50)   not null,
  modified              timestamp     null,
constraint pk_news primary key (id)
)
engine=innodb;

create table stat (
stamp                 timestamp     not null,
ip_hash               varchar(128)  null,
page_id               varchar(50)   not null,
user_agent            varchar(2000) null,
country               varchar(50)   null,
city                  varchar(50)   null,
region                varchar(50)   null
)
engine=myisam;

create view stat_user_agent as
  select
      CASE
          WHEN lower(user_agent) LIKE '%mac%os%' THEN 'Mac OS X'
          WHEN lower(user_agent) LIKE '%ipad%' THEN 'iPad'
          WHEN lower(user_agent) LIKE '%ipod%' THEN 'iPod'
          WHEN lower(user_agent) LIKE '%iphone%' THEN 'iPhone'
          WHEN lower(user_agent) LIKE '%imac%' THEN 'mac'
          WHEN lower(user_agent) LIKE '%android%' THEN 'android'
          WHEN lower(user_agent) LIKE '%linux%' THEN 'linux'
          WHEN lower(user_agent) LIKE '%Nokia%' THEN 'Nokia'
          WHEN lower(user_agent) LIKE '%BlackBerry%' THEN 'BlackBerry'
          WHEN lower(user_agent) LIKE '%win%' THEN
              CASE
                  WHEN user_agent LIKE '%NT 10.0' THEN 'Windows 10'
                  WHEN user_agent LIKE '%NT 6.2%' THEN 'Windows 8'
                  WHEN user_agent LIKE '%NT 6.3%' THEN 'Windows 8.1'
                  WHEN user_agent LIKE '%NT 6.1%' THEN 'Windows 7'
                  WHEN user_agent LIKE '%NT 6.0%' THEN 'Windows Vista'
                  WHEN user_agent LIKE '%NT 5.1%' THEN 'Windows XP'
                  WHEN user_agent LIKE '%NT 5.0%' THEN 'Windows 2000'
                  ELSE 'Windows'
              END
          WHEN user_agent LIKE '%FreeBSD%' THEN 'FreeBSD'
          WHEN user_agent LIKE '%OpenBSD%' THEN 'OpenBSD'
          WHEN user_agent LIKE '%NetBSD%' THEN 'NetBSD'
          WHEN user_agent LIKE '%OpenSolaris%' THEN 'OpenSolaris'
          WHEN user_agent LIKE '%SunOS%' THEN 'SunOS'
          WHEN user_agent LIKE '%OS/2%' THEN 'OS/2'
          WHEN user_agent LIKE '%BeOS%' THEN 'BeOS'
          ELSE 'Unknown'
      END AS os,
      CASE
          WHEN lower(user_agent) LIKE '%edge%'THEN 'Edge'
          WHEN user_agent LIKE '%MSIE%' THEN 'Internet Explorer'
          WHEN lower(user_agent) LIKE '%firefox%' THEN 'Mozilla Firefox'
          WHEN lower(user_agent) LIKE '%chrome%' THEN 'Google Chrome'
          WHEN lower(user_agent) LIKE '%safari%' THEN 'Apple Safari'
          WHEN lower(user_agent) LIKE '%opera%' THEN 'Opera'
          WHEN lower(user_agent) LIKE '%outlook%' THEN 'Outlook'
          ELSE 'Unknown'
      END AS browser,
      CASE
          WHEN user_agent LIKE '%WOW64%' THEN '64 bit'
          WHEN user_agent LIKE '%x64%' THEN '64 bit'
          ELSE '32 bit'
      END AS architecture,
      s.*
   from stat s;

insert into sys_users (user_name, user_pwd, last_login, last_pwd_change, status, role, modifier, modified)
values ('admin', '130516373b3889bd66d7e0abf9cc18875b0f6bdb5d7e66bb19c03a6fa4e65394', null, null, 'AKTIV', 'ADMIN', 'system', current_timestamp);

delete from config_settings where 1=1;
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('enabled-mobile-view', '1', 'Mobil nézet engedélyezése', 'BOOLEAN', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('title', '', 'A weboldal title tulajdonsága', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('header-html', '', 'A weboldal fejléc html sablonja', 'TEXTAREA', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('header-mobile-html', '', 'A weboldal mobilon megjelenő fejléc html sablonja. Ha nem kerül ide semmi akkor automatikusan a header-html értékét veszi', 'TEXTAREA', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('footer-html', '', 'A weboldal lábléc html sablonja', 'TEXTAREA', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('footer-mobile-html', '', 'A weboldal mobilon megjelenő lábléc html sablonja. Ha nem kerül ide semmi akkor automatikusan a footer-html értékét veszi', 'TEXTAREA', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('background-color', '#EDD99E', 'A weboldal háttér színe. Olyan formátumban amit a böngésző css értelmezője elfogad', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('menu-length', '200px', 'A baloldali menü szélessége. Olyan formátumban amit a böngésző css értelmezője elfogad', 'TEXT',  'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('content-length', '600px', 'A fő tartalmi szakasz szélessége. Olyan formátumban amit a böngésző css értelmezője elfogad', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('content-mobile-length', '800px', 'A fő tartalmi szakasz szélessége. Olyan formátumban amit a böngésző css értelmezője elfogad. Ha nem kerül ide semmi automatikusan a content-length értékét veszi', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('meta-description', '', 'Az oldal meta leírása (kereső támogatás)', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('meta-keywords', '', 'Az oldal meta kulcsszavai (kereső támogatás)', 'TEXT', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('main-switch', '1', 'Az oldal fő kapcsolója (ha nincs bejelölve akkor az oldal publikus része nem elérhető)', 'BOOLEAN', 'system', current_timestamp);
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('news-template', '<fieldset><legend><%%title%%></legend><span><%%content%%></span></fieldset>', 'A hir blokkok html sablonja. <%%title%%> karakterek helyére kerül a cim, <%%content%%> karakterek helyére a hir tartalmi része!', 'TEXTAREA', 'system', current_timestamp );
insert into config_settings (setting_key, setting_value, setting_description, setting_type, modifier, modified)
values ('news-mobile-template', '<fieldset><legend style="font-size:20px"><%%title%%></legend><span><%%content%%></span></fieldset>', 'A hir blokkok mobile html sablonja. <%%title%%> karakterek helyére kerül a cim, <%%content%%> karakterek helyére a hir tartalmi része! Ha nincs itt semmi a news-template értékét veszi.', 'TEXTAREA', 'system', current_timestamp );

delete from config_menu where 1=1;
insert into config_menu(id, name, tooltip, menu_type, content, enabled_editor, visible, default_page, order_field, modifier, modified) values
('nyitolap', 'Nyitólap', 'Nyitólap', 'NEWS', '', 1, 1, 0, 10, 'system', current_timestamp);
insert into config_menu(id, name, tooltip, menu_type, content, enabled_editor, visible, default_page, order_field, modifier, modified) values
('elerhetosegek', 'Elérhetőségek', 'Elérhetőségek', 'ALAP','', 1, 1, 0, 100, 'system', current_timestamp);
