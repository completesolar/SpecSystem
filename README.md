
# Installation Readme Document

This document provides instructions on how to install and set up the specsystem application.

## Prerequisites

-   Docker and docker-compose installed on your machine
-   An empty directory to store the installation files


## Installation Steps

1. Copy file `spec/proj/settings_local.py.template` as `spec/proj/settings_local.py`. Modify values if necessary.
    
2. Run the following command to start the installation process:

    `docker-compose up --build`
    
3. Open [http://localhost:80](http://localhost:80) or [https://localhost:443](https://localhost:443). You should see the specsystem login page.
    
4. To log in to the application, you need to create a user account. To do this, first, access the phpldapadmin interface by navigating to [http://localhost:8080](http://localhost:8080/).
    
5. Log in using the following credentials:
    
    `Login DN: cn=admin,dc=company,dc=local
    Password: adminpassword` 
    
10. Once you are logged in to phpldapadmin, click on the "Create new entry here" link on the left-hand side of the page.
    
11. In the "Select object class" dropdown, select "inetOrgPerson" and click the "Create Object" button.
    
12. Fill out the user details on the next page, including a username, first name, last name, and password. Click "Create Object" to save the user.
    
13. Now you can log in to the specsystem application using the username and password you just created.
    

**Note:** If you need to stop the application, press CTRL+C in the terminal window where the application is running. To start the application again, navigate to the directory where the installation files are located and run the "docker-compose up" command again.

## Troubleshooting

-   If you have trouble accessing the application, make sure that no other services are using ports 80, 443, 8080, or 389.
-   If the specsystem service does not start, make sure that the "spec" directory containing the specsystem code is in the same directory as the docker-compose.yml file.


## Loading the ldap.ldif File into phpldapadmin

To load the ldap.ldif file into the phpldapadmin interface, follow these steps:

1.  Log in to the phpldapadmin interface by navigating to [http://localhost:8080](http://localhost:8080/).
    
2.  Log in using the following credentials:   
 `Login DN: cn=admin,dc=company,dc=local
    Password: adminpassword` 
    
3.  Click on the "Import" tab on the left-hand side of the page.
    
4.  In the "LDIF Import" section, click the "Choose File" button and select the ldap.ldif file from your computer.
    
5.  Make sure the "Ignore errors" checkbox is checked, and then click the "Import" button.
    
6.  You should see output indicating that the data was successfully imported into the LDAP server.

## Running Django Migrations

After the installation and setup of the specsystem application, you need to run the Django migrations to set up the database schema. Follow these steps:

1.  Open a new terminal window and navigate to the directory where the installation files are located.
    
2.  Run the following command to access the specsystem container:
    
    `docker exec -it specsystem bash` 
    
3.  Once you are in the container's shell, navigate to the directory where the Django project is located:
    
    `cd spec` 
    
4.  Run the following command to run the Django migrations:

    `python manage.py migrate` 
    
5.  Wait until the migrations complete, and you see a message indicating that the migrations were applied successfully.
    

Now you can use the specsystem application with the database schema set up correctly.

-   Find the original documentation here [IoT Expert](https://github.com/iotexpert/specsystem).