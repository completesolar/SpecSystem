#!/usr/bin/env bash
# start-server.sh

python manage.py collectstatic --noinput

# Build the frontend
cd ./ui/ && npm install && npm run build && cd ..
cp -r ./frontend/static/* ./static

# Build the help files
libreoffice24.8 --norestore --safe-mode --view --convert-to pdf --outdir $APP/help $APP/help/user_guide.docx \
  && libreoffice24.8 --norestore --safe-mode --view --convert-to pdf --outdir $APP/help $APP/help/admin_guide.docx \
  && libreoffice24.8 --norestore --safe-mode --view --convert-to pdf --outdir $APP/help $APP/help/high_level_design.docx

if [ -n "$DJANGO_SUPERUSER_USERNAME" ] && [ -n "$DJANGO_SUPERUSER_PASSWORD" ] ; then
    python manage.py createsuperuser --no-input
fi

if [ -f /app/ext/config/resolv.conf ]; then
    cp /app/ext/config/resolv.conf /etc/
fi

if [ -f /app/ext/config/nginx.conf ]; then
    cp /app/ext/config/nginx.conf /etc/nginx/
fi

if [ -f /app/ext/config/settings_local.py ]; then
    cp /app/ext/config/settings_local.py /app/proj/
fi

gunicorn proj.wsgi --bind 0.0.0.0:8010 --workers 3 &
nginx -g "daemon off;"