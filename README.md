# Open Leave

Open Leave is an AD LDAP integrated leave system based on leave accrual created to be used alongside an Active Directory system. User details are synchronized from AD into a local database without users needing to create user accounts first. User details can also be modified in AD directly without needing to log into the system itself.
Uses the ADLDAP2 for Laravel package. (https://github.com/Adldap2/Adldap2-Laravel)

### Features
* Takes into account public holidays and different user regions.
* Users can create half-day leave requests.
* Users can upload supporting documentation (ex. doctors note).
* Features built-in changelog to allow users to see when new features have been added.
* Built-in email and in-app notification system.
* Automatically calculates and keeps track of user's leave.
* HR account can Approve, Reject, Cancel, Edit and Create leave requests.
* Can accomodate users with 15 and 21 annual leave days respectively.
* Automatic Sick and Family Responsibility Leave rollovers on year ends.
* Filter leave requests by type, user, status, etc.

## Getting Started

### Installing
Clone the repo to your machine.
```
git clone https://github.com/XanCeegor/Open-Leave.git
```
Install dependencies.
```
composer install
```
Rename .env.example to .env. Change your database settings, queue settings and LDAP connection settings.
```
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

QUEUE_CONNECTION=database   #must be database!

LDAP_DOMAIN=#@domain.local
LDAP_HOSTS=#ldap server IP or hostname
LDAP_PORT=389
LDAP_BASEDN=#dc=domain,dc=local
LDAP_USERNAME=#username@domain.local
LDAP_PASSWORD=

#'from' email address for all email notifications
NOTIFICATION_EMAIL_ADDRESS=     
#username of leave account in AD. Used to notify the leave department user account when a request is created
LEAVE_DEPARTMENT_LDAP_USERNAME=  

```
Run the migrations.
```
php artisan migrate
```
Start a dev server
```
php artisan serve
```

## Screenshots
![Imgur](https://imgur.com/Uc5M0H9)

![Imgur](https://imgur.com/iNyVoum)

![Imgur](https://imgur.com/U59zLcX)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
