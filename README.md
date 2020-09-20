# mediafire-php-api
Login and access Mediafire API without api key.

```
<?php
include('mf.php');
$mediafire = new mf('email@gmail.com', 'password'); //login and save session for later.
$session_token = $mediafire->session_token;

$file_info = $mediafire->file_info('fileid'); //get file info in json (must own file or must be shared by owner)

$mediafire->delete_file('fileid'); //delete file and empty trash. (must own file).

$search_results = $mediafire->search_folder('search phrase'); //search entire account by file name.
?>
```

Docs: https://www.mediafire.com/developers/core_api/1.5/getting_started/
