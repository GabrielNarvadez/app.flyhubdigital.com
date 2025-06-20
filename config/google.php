<?php
<<<<<<< HEAD
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));
define('GOOGLE_REDIRECT_URI',  getenv('GOOGLE_REDIRECT_URI'));
=======
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: 'xxxxxxxx.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'xxxxxxxxxxxxxxxx');
define('GOOGLE_REDIRECT_URI',  getenv('GOOGLE_REDIRECT_URI')  ?: 'https://yourdomain.com/auth/google-callback.php');
>>>>>>> 4b2c63115e716a24c849473c7eb09ea277a18027
