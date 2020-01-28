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
![Alt text](https://previews.dropbox.com/p/thumb/AAuXMDEFjRFKPzJGr4Vh60VB29xYff_x0kwS3TpAKlvh7nBcUv4hULLo_khOYNdRy1ZbXRKSq8buErdU_i48LAfO-SD3mYW16zzk4U58Sol_i6XbPtiBcr-fGMrJaxyvyPM97rD4P-0vwMP2nuWAaccUWZeEYkgIC4bzqG3pa2r5_uIgm3M2Wl0GneAZORh3pwMqc5v_9gZotzj1Ew2xT66ovsDmtO6MzBn-l5CPBD79oxPk2zta6qL73cu49uuTLFju94oMPNXeOjB-YNDCEC7l38z7r1PqqjFF5w2myjwkwHKbtM0MCKdidx_lnf9J9t_ZINSCayPJCrRDkzcnK1bV/p.png?fv_content=true&size_mode=5 "Dashboard")

![Alt text](https://previews.dropbox.com/p/thumb/AAuIhfMZHs55FR1cNtd3qFWCyjg8QFiVLc1ZI1rTCsVV0DIIqbSNGL8_NnrdSz8hHo3o0k6TX3D8MXfOARRCp54jQ2PzEWCtm-wZTGn-C_Hn7ZbvAFdWHtgbVbSMDZAS0UO8GBTMvYTR-3RMwHgiEC5PFGOaLeAUdnIAb3dc3KUbRRrXyYErtDUVjxTdoeMoq3Zar8iyOlaj1Ou9DD4se0a_Zenht5ShSCd-DiHMR8FsxGSGO1RewWRFxobH5qKxaZ6XBol9w156r6ChxYVPS6NeRM7rs4vIRdBlzbPX1mXVkL6TnK_cnoJy5hyVBaN9sxsQCZqs3LMPNsIuRcmZwaLE/p.png?fv_content=true&size_mode=5 "HR View")

![Alt text](https://previews.dropbox.com/p/thumb/AAvuvgImOCNc_aG8DJ_hLu-GZWK8nXMUtqbZPAVwvEdNSkpqzdBrxmiji9ynI2eOv-n50pNxHXYtvVNNYir8kgfIjv1B4NRAFLae7vTSFoXnPLQXpK0wbrsdzC7lqWvOcZcPs8RgSqcWqIsglcxVw_sBLxDgwqgh06jNBiokkOp6J-kxOWZMcNZv78CA4WapKoyOr2H8nqd5kYfQV3BF_xL9pGO4f6reTrtySL1cLoOTIuTH8Bs2QBSGvTF7rQ1WhDlXxEHE6G2XpCbc4bHfJZX4zLfcgQ8JHmszyE4-sQ-2twH7293oyoLl0dSH2r2embBiBmdfpEwvIHbUUKxSUaZP/p.png?fv_content=true&size_mode=5 "Create Request")

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
