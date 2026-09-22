FROM nginx:1.27-alpine

RUN rm -f /etc/nginx/conf.d/default.conf

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/ /etc/nginx/conf.d/

RUN chown -R nginx:nginx /var/cache/nginx /etc/nginx

USER nginx

EXPOSE 8080
