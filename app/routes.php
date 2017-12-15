<?php

$app->get('/', 'HomeController:indexQuery');
$app->get('/{name}', 'HomeController:indexGet');

$app->post('/checkLogin/check', 'loginController:checkLogin');

$app->get('/permControl/{userID}', 'permController:permControl');
$app->get('/permReport/{userID}', 'permController:permReport');

$app->get('/recommendedBook/show', 'recomBookController:recomBook');
$app->post('/recommendedBook/add', 'recomBookController:recomBookAdd');
$app->delete('/recommendedBook/delete/{bookID}', 'recomBookController:recomBookDelete');

//Self Check
$app->get('/scReport/today', 'scReportController:scReportToday');
$app->post('/scReport/scReportSearchbyKeyword', 'scReportController:scReportSearchbyKeyword');
$app->post('/scReport/scReportImage', 'scReportController:scReportImage');
$app->post('/scReport/scReportBookStatistic', 'scReportController:scReportBookStatistic');

//Media Upload
$app->post('/mediaController/pictureUpload','mediaController:pictureUpload');
$app->post('/mediaController/videoUpload','mediaController:videoUpload');
$app->get('/mediaController/mediaDisplay','mediaController:mediaDisplay');
$app->delete('/mediaController/mediaDelete/{mediaId}', 'mediaController:mediaDelete');

//Playlist
$app->get('/playlistManagement/playlistDisplay', 'playlistManagement:playlistDisplay');
$app->delete('/playlistManagement/playlistDelete/{playlistId}', 'playlistManagement:playlistDelete');
$app->post('/playlistManagement/createPlaylist','playlistManagement:createPlaylist');
$app->get('/playlistManagement/previewPlaylistItem/{playlistId}', 'playlistManagement:previewPlaylistItem');
$app->put('/playlistManagement/updatePlaylist', 'playlistManagement:updatePlaylist');

//Book Drop
$app->post('/bdReportController/bdReportSearchbyKeyword', 'bdReportController:bdReportSearchbyKeyword');
$app->post('/bdReportController/bdReportImages', 'bdReportController:bdReportImages');
$app->get('/bdReportController/bdReportToday', 'bdReportController:bdReportToday');
$app->post('/bdReportController/bdReportStatistic', 'bdReportController:bdReportStatistic');

//Staff Station
$app->post('/ssReportController/ssReportSearchbyKeyword', 'ssReportController:ssReportSearchbyKeyword');
$app->get('/ssReportController/ssReportToday', 'ssReportController:ssReportToday');
$app->post('/ssReportController/ssReportStatistic', 'ssReportController:ssReportStatistic');

//Security Gate
$app->post('/sgReportController/sgReportSearchbyKeyword', 'sgReportController:sgReportSearchbyKeyword');
$app->post('/sgReportController/sgReportStatistic', 'sgReportController:sgReportStatistic');
$app->get('/sgReportController/sgReportToday', 'sgReportController:sgReportToday');
$app->get('/sgReportController/sgReportCountToday', 'sgReportController:sgReportCountToday');
$app->post('/sgReportController/sgReportCountStatistic', 'sgReportController:sgReportCountStatistic');

//Flap Gate
$app->get('/fgReportController/fgReturnMemberType', 'fgReportController:fgReturnMemberType');
$app->get('/fgReportController/fgReportToday', 'fgReportController:fgReportToday');
$app->post('/fgReportController/fgReportSearchbyKeyword', 'fgReportController:fgReportSearchbyKeyword');
$app->post('/fgReportController/fgReportStatistic', 'fgReportController:fgReportStatistic');
$app->post('/fgReportController/fgReportCountStatistic', 'fgReportController:fgReportCountStatistic');
$app->get('/fgReportController/fgReportCountToday', 'fgReportController:fgReportCountToday');


$app->get('/memberController/memberRecord', 'memberController:memberRecord');


$app->post('/groupController/grouphasChild', 'groupController:grouphasChild');
$app->get('/groupController/groupInitial', 'groupController:groupInitial');
$app->post('/groupController/ChildSearching', 'groupController:ChildSearching');