FROM nginx:alpine

# ARG TZ ${TZ}
# RUN apk --no-cache add tzdata && \
#     cp /usr/share/zoneinfo/${TZ} /etc/localtime && \
#     apk del tzdata

ARG PHP_UPSTREAM_CONTAINER=web-php
ARG PHP_UPSTREAM_PORT=9000
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf


EXPOSE ${NGINX_HOST_HTTP_PORT}