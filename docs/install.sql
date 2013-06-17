--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;



CREATE PROCEDURAL LANGUAGE plpgsql;



SET search_path = public, pg_catalog;



CREATE FUNCTION show_news() RETURNS void
    LANGUAGE plpgsql
    AS $$DECLARE
  u int;
  n int;
BEGIN
	SELECT idnews INTO n
	FROM news 
	WHERE status = 1 AND type = 1 AND publish <= NOW()
	ORDER BY publish DESC
	LIMIT 1;

	IF NOT FOUND THEN
		RETURN;
	END IF;

	FOR u IN SELECT idusers FROM users WHERE status = 1
	LOOP
		BEGIN
			INSERT INTO news_show_users(idusers, idnews) VALUES (u, n);
			EXCEPTION WHEN unique_violation THEN
				UPDATE news_show_users SET count = count + 1, idnews = n WHERE idusers = u;
		END;
	END LOOP;

	UPDATE news SET status = 2 WHERE idnews = n;
END;$$;



CREATE FUNCTION to_email_news() RETURNS void
    LANGUAGE plpgsql
    AS $$DECLARE
  u int;
  n int;
BEGIN
	SELECT idnews INTO n
	FROM news 
	WHERE status = 1 AND type = 2 AND publish <= NOW()
	ORDER BY publish DESC
	LIMIT 1;

	IF NOT FOUND THEN
		RETURN;
	END IF;

	FOR u IN SELECT idusers FROM users WHERE status = 1
	LOOP
		BEGIN
			INSERT INTO emails(idusers, type, id) VALUES (u, 1, n);
		END;
	END LOOP;

	UPDATE news SET status = 2 WHERE idnews = n;
END;$$;



SET default_tablespace = '';
SET default_with_oids = false;



CREATE TABLE codes (
    idusers integer NOT NULL,
    code character(32) NOT NULL UNIQUE,
    type smallint DEFAULT 0 NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    info bytea
);

CREATE UNIQUE INDEX code ON codes USING btree (code, type);

COMMENT ON COLUMN codes.type IS '0 активация пользователя / 1 восстановление пароля  / 2 создание мульти акка';



CREATE TABLE contacts (
    idcontacts serial PRIMARY KEY,
    idusers integer NOT NULL,
    owner integer NOT NULL
);

ALTER TABLE ONLY contacts
    ADD CONSTRAINT contact UNIQUE (idusers, owner);



CREATE TABLE emails (
    idemails serial PRIMARY KEY,
    idusers integer NOT NULL,
    type smallint NOT NULL,
    id integer NOT NULL,
    status smallint DEFAULT 0 NOT NULL,
    "create" timestamp without time zone DEFAULT now() NOT NULL,
    send timestamp without time zone,
    error text
);

COMMENT ON COLUMN emails.type IS '1 - рассылка новости';
COMMENT ON COLUMN emails.status IS '0 - создано / 1 - отправлено / 2 - ошибка';



CREATE TABLE logs (
    idlogs serial PRIMARY KEY,
    type smallint DEFAULT 0 NOT NULL,
    action character varying NOT NULL,
    message character varying NOT NULL,
    title character varying NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    params character varying
);

COMMENT ON COLUMN logs.type IS '0 - информация / 1 - предупреждение / 2 - ошибка';



CREATE TABLE messages (
    idmessages serial PRIMARY KEY,
    idthread integer DEFAULT 0,
    idobjects integer DEFAULT 0,
    writer integer,
    replay integer,
    text text,
    notread smallint DEFAULT 1,
    reservation smallint DEFAULT 0,
    mark smallint DEFAULT 0,
    date timestamp without time zone DEFAULT now(),
    owner integer
);



CREATE TABLE news (
    idnews serial PRIMARY KEY,
    status smallint DEFAULT 0 NOT NULL,
    type smallint DEFAULT 0 NOT NULL,
    title character varying[] NOT NULL,
    brief character varying[] NOT NULL,
    news text[] NOT NULL,
    "create" date DEFAULT now() NOT NULL,
    publish date DEFAULT now() NOT NULL
);

COMMENT ON COLUMN news.status IS '0 - черновик / 1 - опубликована / 2 - обработано';
COMMENT ON COLUMN news.type IS '0 - на сайт / 1 - уведомление / 2 - спам';



CREATE TABLE news_show_users (
    idnews_show_users serial PRIMARY KEY,
    idusers integer NOT NULL UNIQUE,
    idnews integer NOT NULL,
    count integer DEFAULT 1 NOT NULL
);



CREATE TABLE obj_bookmarks (
    idobj_bookmarks serial PRIMARY KEY,
    idusers integer NOT NULL,
    multiuser integer DEFAULT 0 NOT NULL,
    owner integer NOT NULL,
    "create" timestamp without time zone DEFAULT now() NOT NULL
);

CREATE INDEX idusers ON obj_bookmarks USING btree (idusers);
CREATE INDEX multiuser ON obj_bookmarks USING btree (multiuser);



CREATE TABLE obj_category (
    idobj_category serial PRIMARY KEY,
    idobj_type integer DEFAULT 0 NOT NULL,
    tree character varying(12) NOT NULL,
    name character varying NOT NULL,
    alias character varying,
    description character varying,
    img character varying(100),
    moderate smallint DEFAULT 0 NOT NULL,
    disabled smallint DEFAULT 0 NOT NULL
);

COMMENT ON COLUMN obj_category.idobj_type IS '0 - нельзя добавлять / остальное тип категории';

CREATE INDEX tree ON obj_category USING btree (tree);
CREATE INDEX disabled ON obj_category USING btree (disabled);



CREATE TABLE obj_fields (
    idobj_fields serial PRIMARY KEY,
    parent integer DEFAULT 0 NOT NULL,
    type smallint DEFAULT 1 NOT NULL,
    name character varying(50) NOT NULL UNIQUE,
    title character varying(50) NOT NULL,
    units character varying(50),
    orders_values integer DEFAULT 0 NOT NULL
);

CREATE INDEX parent ON obj_fields USING btree (parent);



CREATE SEQUENCE obj_fields_values_orders_seq
    START WITH 10
    INCREMENT BY 10
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE obj_fields_values (
    idobj_fields_values serial PRIMARY KEY,
    parent integer DEFAULT 0 NOT NULL,
    idobj_fields integer NOT NULL,
    value character varying NOT NULL,
    translate smallint DEFAULT 1 NOT NULL,
    orders integer DEFAULT nextval('obj_fields_values_orders_seq'::regclass) NOT NULL,
    disabled smallint DEFAULT 0 NOT NULL,
    count integer DEFAULT 0 NOT NULL
);

CREATE INDEX idobj_fields ON obj_fields_values USING btree (parent, idobj_fields);



CREATE TABLE obj_karma (
    idobj_karma serial PRIMARY KEY,
    idusers integer NOT NULL,
    voted integer NOT NULL,
    comment character varying(500) NOT NULL,
    moderated smallint DEFAULT 0 NOT NULL,
    points smallint DEFAULT 0 NOT NULL
);

COMMENT ON COLUMN obj_karma.moderated IS '-1 отклонена / 0 не модерировался / 1 утверждена';

CREATE INDEX obj_karma_idusers ON obj_karma USING btree (idusers);
CREATE INDEX obj_karma_voted ON obj_karma USING btree (voted);



CREATE SEQUENCE obj_ties_orders_seq
    START WITH 10
    INCREMENT BY 10
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE obj_ties (
    idobj_ties serial PRIMARY KEY,
    idobj_type integer DEFAULT 0 NOT NULL,
    idobj_fields integer DEFAULT 0 NOT NULL,
    idobj_ties_groups integer DEFAULT 0 NOT NULL,
    required smallint DEFAULT 0 NOT NULL,
    filter smallint DEFAULT 0 NOT NULL,
    orders integer DEFAULT nextval('obj_ties_orders_seq'::regclass) NOT NULL,
    params character varying,
    disabled smallint DEFAULT 0 NOT NULL
);

COMMENT ON COLUMN obj_ties.filter IS '0 - не использовать / 1 - по категории / 2 - по главной / 3 - по главной + категории';

ALTER TABLE ONLY obj_ties
    ADD CONSTRAINT obj_ties_idobj_type_key UNIQUE (idobj_type, idobj_fields);



CREATE SEQUENCE obj_ties_groups_orders_seq
    START WITH 10
    INCREMENT BY 10
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE obj_ties_groups (
    idobj_ties_groups serial PRIMARY KEY,
    name character varying NOT NULL,
    orders integer DEFAULT nextval('obj_ties_groups_orders_seq'::regclass) NOT NULL
);



CREATE TABLE obj_type (
    idobj_type serial PRIMARY KEY,
    name character varying NOT NULL
);



CREATE TABLE objects (
    idobjects serial PRIMARY KEY,
    idobj_category integer NOT NULL,
    idobj_type integer NOT NULL,
    idusers integer NOT NULL,
    multiuser integer DEFAULT 0 NOT NULL,
    moderate smallint DEFAULT 0 NOT NULL,
    spam smallint DEFAULT 0 NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    show timestamp without time zone DEFAULT now() NOT NULL,
    object character varying[],
    multilang character varying[],
    lifetime_date timestamp without time zone,
    on_map smallint DEFAULT 0 NOT NULL,
    disabled smallint DEFAULT 0 NOT NULL
);

COMMENT ON COLUMN objects.moderate IS '0 не нуждается / 1 на модерацию / 2 промодерирован';
COMMENT ON COLUMN objects.spam IS '-1 не спам / 0 не определено / 1 возможно / 2 спам';

CREATE INDEX objects_moderate ON objects USING btree (moderate);
CREATE INDEX objects_show ON objects USING btree (show);
CREATE INDEX objects_spam ON objects USING btree (spam);
CREATE INDEX objects_category ON objects USING btree (idobj_category);



CREATE TABLE services (
    idservices serial PRIMARY KEY,
    idusers integer NOT NULL,
    id character varying NOT NULL,
    service character varying NOT NULL,
    friends_count integer DEFAULT 0 NOT NULL,
    url_social character varying,
    social_info bytea,
    date timestamp without time zone DEFAULT now() NOT NULL
);

CREATE UNIQUE INDEX id ON services USING btree (id, service);



CREATE TABLE users (
    idusers serial PRIMARY KEY,
    multiuser integer DEFAULT currval('users_idusers_seq'::regclass) NOT NULL,
    role smallint DEFAULT 1 NOT NULL,
    profile smallint DEFAULT 1 NOT NULL,
    status smallint DEFAULT 0 NOT NULL,
    name character varying NOT NULL,
    email character varying,
    pass character varying,

    tel character varying,
    country integer,
    city integer,
    me character varying,
    language character varying,
    company character varying,
    karma integer DEFAULT 0 NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    subscription integer[] DEFAULT '{1,2}'::integer[] NOT NULL,
    settings character varying[] DEFAULT '{0,0,1}'::integer[] NOT NULL,
    access_fields character varying DEFAULT ''::character varying NOT NULL,
    avatar character varying,
    main_language character varying
);

COMMENT ON COLUMN users.role IS '0 - GUEST / 1 - USER / 2 - MODER / 3 - ADMIN';
COMMENT ON COLUMN users.status IS '0 - не активирован / 1 - активирован / 2 - забанен';

CREATE INDEX email ON users USING btree (email);

INSERT INTO users (role, profile, status, name, email, pass) VALUES (3, 1, 1, 'Admin', 'admin@geojoy.com', '$6$rounds=75076$GEc4dBJSpYANZ6wt$eyKJiu/JuWr6ZbuiHN28CRBWXDLlVRH7snePEo51xjujDYlsGwMXNGwVJaVz3XcWanvIEPxVeoSOG2zpa2Ec20');



REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--