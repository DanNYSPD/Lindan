select * from usuario;

insert into usuario (id,nombre,edad,	) values (3,'Mariana',22,'mari');
insert into usuario (id,nombre,edad,pass) values (4,'Lupita',21,'lupe');

insert into usuario (id,nombre,edad,pass) values (6,'ñoño',21,'hernández');



CREATE TABLE public.usuarioSerial
(
  id serial not null,
  nombre character varying(50),
  edad integer,
  pass character varying(100),
  CONSTRAINT usuarioSerial_pkey PRIMARY KEY (id)	
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.usuarioSerial
  OWNER TO postgres;

GRANT ALL ON TABLE public.usuarioSerial TO dan;

-- conterase por pgsql : >psql -U postsgres


-- listar roles :   >\du
-- otorgar permisos de bd a un rol(o usuario) : > grant all privileges on database bdescuela to dan;


--  otorgar permisos de todas las tablas aun role: >grant all PRIVILEGES  on all tables in schema public to dan;

-- otorgar permisos de todas las tablas a un role:>GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE users TO dan;

-- listar databases : >\l

--cambiar de bd :> \c databaseName

-- carmbiar de dueño:  >alter database bdescuela owner to postgres;

https://phpdelusions.net/pdo/pdo_wrapper

