FROM elastic/filebeat:9.3.1

COPY filebeat.yml /usr/share/filebeat/filebeat.yml

USER root
RUN chown root:filebeat /usr/share/filebeat/filebeat.yml && \
    chmod 640 /usr/share/filebeat/filebeat.yml

USER filebeat
