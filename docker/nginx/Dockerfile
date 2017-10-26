FROM alpine:3.4

MAINTAINER Yuriy Prokopets <yuriy.prokopets@gmail.com>

RUN apk add --update nginx
RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

ADD nginx.conf /etc/nginx/
ADD site.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server fpm:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser -D -g '' -G www-data www-data
RUN chown -R www-data:www-data /var/lib/nginx/

CMD ["nginx"]

EXPOSE 80
