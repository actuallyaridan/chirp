# Chirp

![Chirp logo](/src/images/users/chirp/banner.png)

**Chirp** is an open source social media project designed to replicate the feel of Twitter. but be better. Chirp is built with PHP, HTML, JS, CSS, and SQLite.

## Status

### What's Working
- [x] Account creation
- [x] Posting
- [x] Replying
- [x] Liking
- [x] Account editing
- [x] Following

### What's Not Working
- [ ] Reposts
- [ ] Viewing reposts on accounts

### What's Planned
- [ ] Post editing
- [ ] Post deletion
- [ ] Search
- [ ] Trends
- [ ] "For You" timeline

## Screenshot

![Chirp on Desktop](/src/images/screenshots/chirpDesktop.png)

## Contributing to Chirp

Chirp's source code is open source, and contributions are welcome! You can fork this repository, make changes, and submit a pull request. If you contribute, include your Chirp username to receive a contributor badge next to your name.

### Working with the Code

#### Using PHP's Built-in Server

1. Clone this repository to your desired location.
2. Navigate to the project directory:

    ```sh
    php -S localhost:port
    ```

#### Using Apache/XAMPP

1. Ensure you have `PHP` and `PDO` with SQLite support installed.
2. Clone the repository:

    ```sh
    git clone https://github.com/actuallyaridan/chirp
    mv chirp /var/www/
    ```

    - *Note: Replace `/var/www/` with the directory used by your Apache or XAMPP installation.*

3. Ensure the directory is empty before moving Chirp. Placing it in a non-empty folder like `/htdocs/chirp` may cause issues.

#### Database

Chirp uses SQLite as its database. The actual database is not included in the repository due to sensitive information, but a blank sample database (`chirp.db.sample`) is provided.

To use it:

1. Rename `chirp.db.sample` to `chirp.db`.
2. Place it above the root directory of your PHP web server to secure it.
3. Data will populate automatically as you use the application.

To create an admin account:

1. Manually generate an invite code.
2. Sign up with the username "chirp".

*Note: A more user-friendly system for managing permissions and privileges is planned.*

## Forking Chirp

You are free to fork Chirp and use its code as a foundation for your project. However, please adhere to the following guidelines:

- **Rebrand your project:** Avoid using any Chirp branding.
- **Credit us:** Provide credit to the original contributors or link to this repository.

Chirp is licensed under the MIT license.
