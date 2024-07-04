
# Installation Readme Document

This document provides instructions on how to install and set up the specsystem application.

## Prerequisites

-   Docker and docker-compose installed on your machine
-   An empty directory to store the installation files


## Installation Steps

1. Copy file `spec/proj/settings_local.py.template` as `spec/proj/settings_local.py`. Modify values if necessary.
    
2. Run the following command to start the installation process:

    `docker-compose up --build`
    
3. Open [http://localhost:8087](http://localhost:8087). You should see the specsystem login page.

**Note:** If you need to stop the application, press CTRL+C in the terminal window where the application is running. To start the application again, navigate to the directory where the installation files are located and run the "docker-compose up" command again.

## Troubleshooting

-   If you have trouble accessing the application, make sure that no other services are using ports 8888, 443, or 8087.

## Running Django Migrations

After the installation and setup of the specsystem application, you need to run the Django migrations to set up the database schema. Follow these steps:

1. Using any MSSQL client, login to the server using data from `settings_local.py` and create DB `spec_qa`(defined in settings, value `DATABASES.default.NAME`); 

2. Run the following command to access the specsystem container:
    
    `docker-compose exec specsystem bash`
    
3.  Run the following command to run the Django migrations:

    `python manage.py migrate` 
    
4.  Wait until the migrations complete, and you see a message indicating that the migrations were applied successfully.
    

Now you can use the specsystem application with the database schema set up correctly.

-   Find the original documentation at [Github repo page](https://github.com/iotexpert/specsystem).