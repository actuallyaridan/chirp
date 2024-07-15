# Chirp

![Chirp logo](/src/images/users/chirp/banner.png)

a social media project meant to mimic the feel of twitter but be better

## Dealing with code

### Using PHPs built-in server

_clone this git repo and put it where you like it and `cd` into it_

```sh
php -S localhost:port
```

### Using Apache/XAMPP

_have `PHP` and `PDO` with SQLite support installed_

```sh
git clone https://github.com/actuallyaridan/chirp
mv chirp /var/www/
# Or other place your Apache or XAMPP install uses for hosting coontent
```

**BTW that folder should be empty - if it's something like `/htdocs/chirp` it will 99% break**

### Database

This project is currently using SQLite but rn there's no database file for it besides the refrence in the code
