FROM nginx:alpine

COPY ./nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf
