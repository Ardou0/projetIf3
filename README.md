# Docker NGINX PHP MySQL PhpMyadmin

Easy PHP MySQL development with Docker and Docker Compose.

With this project you can quickly run the following:

- [NGINX](https://hub.docker.com/_/nginx)
- [PHP](https://hub.docker.com/_/php)
- [phpMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin/)
- [MySQL](https://hub.docker.com/_/mysql/)

Contents:

- [Requirements](#requirements)
- [Configuration](#configuration)
- [Usage](#usage)

## Requirements

Make sure you have the latest versions of **Docker** and **Docker Compose** installed on your machine.

Clone this repository or copy the files from this repository into a new folder. In the **docker-compose.yml** file you may change the IP address (in case you run multiple containers) or the database from MySQL to MariaDB.

Make sure to [add your user to the `docker` group](https://docs.docker.com/install/linux/linux-postinstall/#manage-docker-as-a-non-root-user) when using Linux.

## Configuration

Edit the `.env` file to change the default IP address, MySQL root password and Database name.


## Usage

### Starting containers

You can start the project/containers with the `start.bash` script :

```
bash start.bash
```

### Stopping containers

```
bash stop.bash
```

### Removing containers

While using the command to stop, it also remove all containers, no need to run another command :

```
bash start.bash
```

It also remove the database volume.

### Creating database dumps

```
./export.sh
```


### phpMyAdmin

You can also visit `http://127.0.0.1:8000` to access phpMyAdmin after starting the containers.

The default username is `root`, and the password is the same as supplied in the `.env` file.
