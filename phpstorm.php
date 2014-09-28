<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-09-22 
 */

$isNotCalledFromCommandLineInterface = (PHP_SAPI !== 'cli');

$usage = 'Usage:' . PHP_EOL .
    basename(__FILE__) . ' <path to new version> [<group name>]';

$pathToCurrentInstallation = '/usr/share/phpstorm';
$pathToExecutable = '/usr/bin/phpstorm.sh';
$pathToTemporalPath = '/tmp/bazzline_' . md5(__FILE__);
$pathToBackup = '/tmp';

/**
 * @param $command
 * @return array
 */
function executeCommand($command)
{
    $lines = array();
    $return = null;
    exec($command, $lines, $return);

    if ($return > 0) {
        throw new RuntimeException(
            'following command created an error: "' . $command . '"' . PHP_EOL .
            'return: "' . $return . '"'
        );
    }

    return $lines;
}

try {
    $startTime = microtime(true);
    //begin of validation
    if ($isNotCalledFromCommandLineInterface) {
        throw new RuntimeException(
            'command line script only'
        );
    }

    $currentWorkingDirectory = getcwd();
    $relativeScriptFilePath = array_shift($argv);
    $pathToNewVersion = array_shift($argv);
    $groupName = array_shift($argv);

    if (is_null($pathToNewVersion)) {
        throw new InvalidArgumentException(
            'path to new version is mandatory'
        );
    }

    if (!is_file($pathToNewVersion)) {
        throw new InvalidArgumentException(
            'provided path to new version is not a file'
        );
    }
    //end of validation

    //begin of variable setting
    $newVersionFileName = basename($pathToNewVersion);
    $start = 9; //strlen('PhpStorm-');
    $end = 7;   //strlen('.tar.gz');

    $version = substr($newVersionFileName, $start, -$end);
    //end of variable setting

    //begin of backup
    if (is_file($pathToExecutable)) {
        $command = 'sudo rm -fr ' . $pathToExecutable;
        executeCommand($command);
    }

    if (is_dir($pathToCurrentInstallation)) {
        $currentDate = date('Y_m_d');
        $pathNameToBackup = $pathToBackup . DIRECTORY_SEPARATOR . 'phpstorm_' . $currentDate . '.tar.gz';

        if (is_dir($pathNameToBackup)) {
            throw new RuntimeException(
                'backup "' . $pathNameToBackup . '" already exist, you have to move or remove it manually'
            );
        }

        echo 'creating backup named "' . $pathNameToBackup . '"' . PHP_EOL;
        $command = 'tar --ignore-failed-read -zcf ' . $pathNameToBackup . ' ' . $pathToCurrentInstallation;
        executeCommand($command);

        echo 'removing old installation' . PHP_EOL;
        $command = 'sudo rm -fr ' . $pathToCurrentInstallation;
        executeCommand($command);
    }

    if (is_dir($pathToTemporalPath)) {
        $command = 'rm -fr ' . $pathToTemporalPath;
        executeCommand($command);
    }
    //end of backup

    //begin of installing new version
    $command = 'mkdir ' . $pathToTemporalPath;
    executeCommand($command);

    echo 'unpacking new version' . PHP_EOL;
    $command = 'tar -ztf ' . $pathToNewVersion;
    $lines = executeCommand($command);

    $unpackedDirectoryName = array_shift(explode('/', $lines[0]));

    $command = 'tar -zxf ' . $pathToNewVersion . ' -C ' . $pathToTemporalPath;
    $lines = executeCommand($command);

    echo 'installing version ' . $version . PHP_EOL;
    $command = 'sudo mv ' . $pathToTemporalPath . DIRECTORY_SEPARATOR . $unpackedDirectoryName . ' ' . $pathToCurrentInstallation;
    executeCommand($command);

    echo 'creating symlink "' . $pathToExecutable . '"' . PHP_EOL;
    $command = 'sudo ln -s ' . $pathToCurrentInstallation . '/bin/phpstorm.sh ' . $pathToExecutable;
    executeCommand($command);

    if (!is_null($groupName)) {
        echo 'updating group to ' . $groupName . PHP_EOL;
        $command = 'sudo chgrp -R ' . $groupName . ' ' . $pathToCurrentInstallation;
        executeCommand($command);

        echo 'setting permissions' . PHP_EOL;
        $command = 'sudo chmod -R 770 ' . $pathToCurrentInstallation;
        executeCommand($command);
    }

    $command = 'rm -fr ' . $pathToTemporalPath;
    executeCommand($command);
    //end of installing new version

    //begin of praise myself
    echo 'done' . PHP_EOL;
    echo '----' . PHP_EOL;
    echo 'runtime: ' . round((microtime(true) - $startTime), 2) . ' seconds' . PHP_EOL;

    $memoryUsage = memory_get_usage(true);
    echo 'memory usage: ';
    if ($memoryUsage < 1024) {
        echo $memoryUsage . ' bytes';
    } else if ($memoryUsage < 1048576) {
        echo round(($memoryUsage / 1024), 2) . ' kilobytes';
    } else {
        echo round(($memoryUsage / 1048576), 2) . ' megabytes';
    }
    echo PHP_EOL;
    //end of praise myself
} catch (Exception $exception) {
    echo 'Error' . PHP_EOL;
    echo '----------------' . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;
    echo '--------' . PHP_EOL;
    echo $exception->getTraceAsString() . PHP_EOL;
    echo PHP_EOL;
    echo $usage . PHP_EOL;
    echo '----------------' . PHP_EOL;
}
