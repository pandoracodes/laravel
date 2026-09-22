FROM nginx:1.27-alpine

RUN rm -f /etc/nginx/conf.d/default.conf

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/ /etc/nginx/conf.d/

# Pastikan user nginx (uid 101 bawaan image ini) punya akses ke path yang
# dia butuh tulis (cache/temp, semuanya sudah diarahkan ke /tmp lewat
# nginx.conf, tapi baris ini jaga-jaga untuk /var/cache/nginx bawaan image).
RUN chown -R nginx:nginx /var/cache/nginx /etc/nginx

# Full rootless: master process TIDAK pernah jadi root, sehingga listen
# di port >1024 saja (8080) tanpa perlu capability NET_BIND_SERVICE apa pun.
USER nginx

EXPOSE 8080
