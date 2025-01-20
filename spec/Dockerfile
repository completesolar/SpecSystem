FROM nikolaik/python-nodejs:python3.10-nodejs16
ENV APP=/app
ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1

RUN echo "deb http://deb.debian.org/debian buster main contrib" > /etc/apt/sources.list \
  echo "deb http://deb.debian.org/debian-security buster/updates main contrib" >> /etc/apt/sources.list \
  echo "deb http://deb.debian.org/debian buster-updates main contrib" >> /etc/apt/sources.list

RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    gettext \
    libsasl2-dev \
    nginx \
    python-dev \
    libldap2-dev \
    libreoffice \
    openssl \
    ttf-mscorefonts-installer \
    unixodbc-dev \
    unixodbc \
    vim \
    odbcinst
    
# This block loads the drivers required to make pyodbc and microsoft sql server work
# It came from https://docs.microsoft.com/en-us/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver16
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update && apt-get install -y curl apt-transport-https gnupg2
RUN TEMP_DEB="$(mktemp)" \
 && ARCH=$(dpkg --print-architecture) \
 && wget -O "$TEMP_DEB" "https://packages.microsoft.com/debian/11/prod/pool/main/m/msodbcsql18/msodbcsql18_18.2.1.1-1_$ARCH.deb" \
 && yes | dpkg --skip-same-version -i "$TEMP_DEB" \
 && rm -f "$TEMP_DEB"

RUN TEMP_DEB="$(mktemp)" \
 && ARCH=$(dpkg --print-architecture) \
 && wget -O "$TEMP_DEB" "https://packages.microsoft.com/debian/11/prod/pool/main/m/mssql-tools18/mssql-tools18_18.2.1.1-1_${ARCH}.deb" \
 && yes | dpkg --skip-same-version -i "$TEMP_DEB" \
 && rm -f "$TEMP_DEB"

RUN wget -O "/tmp/libreoffice.tar.gz" "https://download.documentfoundation.org/libreoffice/stable/24.8.4/deb/x86_64/LibreOffice_24.8.4_Linux_x86-64_deb.tar.gz" \
 && tar -xvf /tmp/libreoffice.tar.gz -C /tmp \
 && dpkg -i /tmp/LibreOffice_24.8.4.*_Linux_x86-64_deb/DEBS/*.deb \
 && rm -rf /tmp/LibreOffice_24.8.4.*_Linux_x86-64_deb /tmp/libreoffice.tar.gz

RUN echo "[ODBC]\n\
Trace = No\n\
TraceFile = /dev/null\n\
ForceTrace = No\n\
Pooling = No\n\
" >> /etc/odbc.ini \
 && echo "[mssql]\n\
Driver = ODBC Driver 18 for SQL Server\n\
Server = sqlexpress\n\
Port = 1433\n\
Database = spec_qa\n\
Encrypt = Yes\n\
TrustServerCertificate = Yes\n\
" >> /etc/odbc.ini

# Set the working directory to /app
WORKDIR $APP

# Copy the current directory contents into the container at /app
COPY ./requirements.txt $APP/requirements.txt

# Setup nginx
COPY nginx.default /etc/nginx/sites-available/default
COPY nginx.conf /etc/nginx/nginx.conf
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
  && ln -sf /dev/stderr /var/log/nginx/error.log

# Install the dependencies; build the ui folder; publish frontend
RUN pip install --upgrade pip
RUN pip install -r $APP/requirements.txt

STOPSIGNAL SIGTERM
ENTRYPOINT ["/app/start-server.sh"]
