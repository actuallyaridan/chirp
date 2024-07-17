# Chirp

![Chirp logo](/src/images/users/chirp/banner.png)

a social media project meant to mimic the feel of twitter but be better

## Status

### What's working
- [x] Account creation
- [x] Posting
- [x] Replying
- [x] Liking

### What isn't working
- [ ] Reposts
- [ ] Viewing likes and reposts on accounts

### What's planned
- [ ] Account editing
- [ ] Post editing
- [ ] Post deletion
- [ ] Following
- [ ] Search
- [ ] Trends
- [ ] "For you" timeline

## Screenshot

![Chirp on Desktop](/src/images/screenshots/chirpDesktop.png)


## Contributing to Chirp
Chirps source code (as you can see) is open source. You can freely fork this repo, make changes and then create a pull request.
If you'd like to, provide your Chirp username and you'll gain a cotrnibutros badge next to your name.

### Dealing with code

#### Using PHPs built-in server

_clone this git repo and put it where you like it and `cd` into it_

```sh
php -S localhost:port
```

#### Using Apache/XAMPP

_have `PHP` and `PDO` with SQLite support installed_

```sh
git clone https://github.com/actuallyaridan/chirp
mv chirp /var/www/
# Or other place your Apache or XAMPP install uses for hosting coontent
```

**BTW that folder should be empty - if it's something like `/htdocs/chirp` it will 99% break**

#### Database

This project is currently using SQLite. Chrips database is not included in GitHub, as it contains sensntive information that will not be shared publicly. There are however refereces in the PHP files, so you can create an empty replica if you'd like to.

## Forking Chirp

You can freely made copies of Chirp and use Chirps code as a base for your procject. However, in order to be cool, please follow these guidelines:

• Rebrand your project: Please refrain from uisng any Chirp branding in your project

• Credit us: You should provide credit to the people who have contibuted to this project, or link this repo

Other than that, Chirp uses the MIT license.



