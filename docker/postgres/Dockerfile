FROM postgres:18.0

COPY ./docker/postgres/config/postgres.conf /etc/postgresql/postgresql.conf
COPY ./docker/postgres/init/* /docker-entrypoint-initdb.d/

RUN chmod +x /docker-entrypoint-initdb.d/*

RUN rm -rf /docker