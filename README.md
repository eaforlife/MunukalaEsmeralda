# Munukala Esmeralda PWA
Android/iOS PWA - Plant Database:

1. Database Design (MySQL, IndexDB)
2. Front End Design (HTML, Javascript, CSS)
3. Backend Design (PHP, Javascript, Firebase)
4. Android/iOS PWA Design (Javascript, Manifest)

Input for web UI
- Search Plant Entry
- CRUD plant entry
- Download android app prompt

Input for Android/iOS PWA
- Search for Plant Entry
- Download plant entry for offline viewing
- Delete downloaded plant entry

Process for web UI:
1. User Login (Front-end web based) checks via Javascript and PHP
2. CRUD (Create, Read, Update and Delete) for logged in user (admin) via Javascript and PHP
3. PHP handles CRUD process while Javascript is the middle man

Process for Android/iOS PWA:
4. Javascript fetch database entry via PHP
5. Javascript handles mobile app installs
7. Javascript handles offline viewing for the mobile app
8. Javascript fetches saved content using Android's IndexDB for offline content
9. Javascript handles content update when user is back online
10. Javascript handles deletetion of saved content in phone's IndexDB if user opted to

Output for web UI
- Dynamic real-time content using Javascript
- Detailed output of plant entries
- Showcase features of products
- Download app event prompt

Output for Android/iOS PWA
- Dynamic content using javascript
- Static content using local IndexDB for offline viewing (if user opted)


