<?php
/**
 * Created by PhpStorm.
 * User: mrred
 * Date: 12.01.2019
 * Time: 18:21
 */

require_once('autoload.php');

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'download') {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, '../temp/'.$_GET['filename']));
        finfo_close($finfo);

        header('Content-Disposition: attachment; filename='.basename($_GET['filename']));

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        header('Content-Length: ' . filesize('../temp/'.$_GET['filename']));

        ob_clean();
        flush();
        readfile('../temp/'.$_GET['filename']);
        exit;
    } else

    if ($_GET['action'] == 'startWsServer') {
        exec('nohup php -f ws.php > log/ws.log 2>&1 & echo $!');
    }
}
