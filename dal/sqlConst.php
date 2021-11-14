<?php

class SqlConst{

  const SETTING_SELECT_BY_KEY = "SELECT setting_value
                                  FROM config_settings
                                  WHERE setting_key = :key";

  const SETTING_SELECT_ALL = "SELECT * FROM config_settings ORDER BY setting_key";

  const SETTING_UPDATE = "UPDATE config_settings
                          SET
                          setting_value = :setting_value,
                          modifier = lower(:user_name),
                          modified = current_timestamp
                          WHERE setting_key = :setting_key ";

  const MENU_COUNT = "SELECT count(*) cnt FROM config_menu WHERE id=:id";

  const MENU_SELECT_ALL = "SELECT * FROM config_menu
                            WHERE deleted is null ORDER BY order_field";

  const MENU_SELECT_DEFAULT = "SELECT x.id page from (
                                select id from config_menu where default_page = 1
                                union
                                select id from config_menu where default_page = 0) x
                                limit 1;
                                ";

  const MENU_SELECT_VISIBLE = "SELECT id, name, tooltip
                                FROM config_menu
                                WHERE visible = 1 AND deleted is null ORDER BY order_field";

  const MENU_SELECT_BY_ID = "SELECT * FROM config_menu
                            WHERE (id = :id or :id is null)
                            AND visible in (1, :visible)
                            AND deleted is null
                            ORDER BY order_field";

  const MENU_UPDATE_TEMPLATE = "UPDATE config_menu SET
                              <%%field_name%%>=:field_value,
                              modifier = lower(:modifier),
                              modified = current_timestamp
                              where id = :id
                              and deleted is null
                            ";
  const MENU_REMOVE_DEFAULT = "UPDATE config_menu SET
                               default_page = 0
                               where deleted is null";

  const MENU_INSERT = "INSERT INTO config_menu
  (id, name, menu_type, visible, order_field, default_page, modifier, modified) VALUES
  (:id, :field_value, 'ALAP', 0,
  (select COALESCE(max(order_field), 0)+10 from config_menu cm2),
  0,
  lower(:modifier), current_timestamp)";

  const MENU_REMOVE = "UPDATE config_menu
                       SET
                       deleted  = current_timestamp,
                       deleted_by = :user_name
                       WHERE id = :id ";

  const NEWS_SELECT = "SELECT * FROM news
                        WHERE menu_id = :menu_id
                        AND visible in (1, :visible)
                        and deleted is null
                        ORDER BY highlight desc, created DESC";

  const NEWS_SELECT_BY_ID = "SELECT * FROM news WHERE id = :id and deleted is null";

  const NEWS_REMOVE = "UPDATE news
                       SET
                       deleted  = current_timestamp,
                       deleted_by = :user_name
                       WHERE id = :id";

  const NEWS_ADD = "INSERT INTO news (id, menu_id, title, content, visible, highlight, creator, created, modified, modifier)
                    VALUES (:id, :menu_id, :title, :content, :visible, :highlight, :user, current_timestamp, current_timestamp, :user)";

  const NEWS_MODIFY_TITLE = "UPDATE news
                              SET
                                title = :title,
                                modifier = :user,
                                modified = current_timestamp
                              WHERE id = :id and deleted is null";
  const NEWS_MODIFY_CONTENT = "UPDATE news
                                SET
                                content = :content,
                                modifier = :user,
                                modified = current_timestamp
                                WHERE id = :id and deleted is null";
  const NEWS_MODIFY_VISIBLE = "UPDATE news
                                SET
                                visible = :visible,
                                modifier = :user,
                                modified = current_timestamp
                                WHERE id = :id and deleted is null";
  const NEWS_MODIFY_HIGHLIGHT = "UPDATE news
                    SET
                    highlight = :highlight,
                    modifier = :user,
                    modified = current_timestamp
                    WHERE id = :id and deleted is null";

  const STAT_ADD = "INSERT INTO stat (stamp, ip_hash, page_id, user_agent, country, city, region)
                    values (current_timestamp,:ip_hash, :page_id, :user_agent, :country, :city, :region )";

  const STAT_VISITORS_COUNT = "
  select
  x.*
  from (
  select distinct
    c.name menu_name,
    (select count(*) from stat s1
      where s1.page_id = s.page_id
      and s1.stamp between :date_from and :date_to
      ) all_page_load,
    (select count(distinct page_id, ip_hash) from  stat s2
      where s2.page_id = s.page_id
      and s2.stamp between :date_from and :date_to) visitor_count
  from stat s,
  config_menu c
  where s.page_id = c.id
  and c.deleted is null
  ) x
  order by x.all_page_load desc
  ";

  const STAT_BROWSERS = "
  select browser item_name, count(*) item_count
  from stat_user_agent
  where  stamp between :date_from and :date_to
  group by browser
  order by count(*) desc
  ";

  const STAT_OS = "
  select os item_name, count(*) item_count
  from stat_user_agent
  where  stamp between :date_from and :date_to
  group by os
  order by count(*) desc
  ";

  const STAT_CITY =
  "select city item_name, count(*) item_count
  from stat
  where stamp between :date_from and :date_to
  and city is not null
  group by city
  order by count(*) desc
  ";

  const STAT_REGION =
  "select region item_name, count(*) item_count
  from stat
  where stamp between :date_from and :date_to
  and region is not null
  group by region
  order by count(*) desc
  ";

  const STAT_COUNTRY =
  "select country item_name, count(*) item_count
  from stat
  where stamp between :date_from and :date_to
  and country is not null
  group by country
  order by count(*) desc
  ";

  const USER_BAD_LOGIN_COUNT = "SELECT COUNT(*) cnt FROM bad_logins
  WHERE created > addtime(CURRENT_TIMESTAMP, SEC_TO_TIME(-1*:min*60))
  AND lower(user_name) = lower(:user_name)";

  const USERS =
  "SELECT
  u.*,
  (select count(*) from sys_session s where lower(s.user_name) = lower(u.user_name)
  and last_activity > addtime(CURRENT_TIMESTAMP, SEC_TO_TIME(-1*30*60))
  and logout_time is null) active_session,
  (select count(*) from bad_logins b where lower(b.user_name) = lower(u.user_name)) bad_logins
  from sys_users u
  order by u.user_name";

  const USER_BY_NAME =
  "SELECT * FROM sys_users WHERE lower(user_name) = lower(:user_name)";

  const USER_SET_PWD ="UPDATE sys_users SET
                      user_pwd=:password,
                      last_pwd_change=current_timestamp
                      WHERE lower(user_name)=lower(:user_name)";

  const USER_CLEAR_BAD_LOGINS = "DELETE FROM bad_logins WHERE lower(user_name) = lower(:user_name)";
  const USER_UPDATE_LAST_LOGIN = "UPDATE sys_users SET last_login=current_timestamp WHERE lower(user_name)=lower(:user_name)";
  const USER_ADD_BAD_LOGIN = "INSERT INTO bad_logins (user_name, created) VALUES (lower(:user_name), current_timestamp)";

  const USER_ADMIN_COUNT = "SELECT count(*) cnt from sys_users u where role = 'ADMIN' and lower(user_name) != lower(:user_name)";

  const USER_ADD = "INSERT INTO sys_users (user_name, user_pwd, last_login, last_pwd_change, status, role, modifier, modified)
                    VALUES (lower(:user_name), :user_pwd, null, null, 'AKTIV', :role, :modifier, current_timestamp)";

  const USER_UPDATE = "UPDATE sys_users SET
                      status = :status,
                      role = :role,
                      modifier = lower(:modifier),
                      modified = current_timestamp
                      WHERE user_name = lower(:user_name)";

  const SESSION_START = "INSERT INTO sys_session
  (id, ip, browser_hash, user_name,  user_role, login_time, last_activity, logout_time,  session_data)
  VALUES
  (:id, :ip, :browser_hash, lower(:user_name), upper(:user_role), current_timestamp, current_timestamp, :logout_time, :session_data)";

  const SESSION_UPDATE_LAST_ACTIVITY = "UPDATE sys_session SET last_activity = CURRENT_TIMESTAMP WHERE id = :id";

  const SESSION_SELECT_BY_ID = "SELECT * FROM sys_session where id = :id and ip = :ip and browser_hash = :browser_hash";

  const SESSION_VALID_COUNT =
  "SELECT count(*) cnt FROM sys_session
  WHERE id = :id
  AND ip = :ip
  AND browser_hash = :browser_hash
  AND logout_time is null
  AND last_activity > addtime(CURRENT_TIMESTAMP, SEC_TO_TIME(-1*:session_length*60))
  AND login_time > addtime(CURRENT_TIMESTAMP, SEC_TO_TIME(-1*24*60*60))";

  const SESSION_KILL =
  "UPDATE sys_session
  SET logout_time = CURRENT_TIMESTAMP
  WHERE id = :id
  AND ip = :ip
  AND browser_hash = :browser_hash";

  const SESSION_KILL_BY_NAME = "UPDATE sys_session
                                SET logout_time = CURRENT_TIMESTAMP
                                WHERE lower(user_name) = lower(:user_name)";


  const CODE_SELECT_VALUE = "SELECT code_value FROM code
                            WHERE lower(id) = lower(:id)
                            AND lower(code_type) = lower(:code_type)";

  const NAPLO_SELECT_WITH_LIMIT= "SELECT * FROM naplo ORDER BY datum DESC LIMIT :limit";

}
?>
