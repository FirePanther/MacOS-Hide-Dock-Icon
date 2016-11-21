<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          WTFPL <http://www.wtfpl.net/>
 * @link             https://github.com/FirePanther/MacOS-Hide-Dock-Icon
 */

// needs permissions to rewrite Info.plist or codesign.
$user = posix_getpwuid(posix_geteuid());
if ($user['name'] !== 'root') die("Please run this script as root (sudo).\n");

// ask for app name
echo 'App name: ';
$fh = fopen('php://stdin', 'r');
$inpCase = trim(fgets($fh));
$inp = strtolower($inpCase);

// search for this app

if (file_exists($inpCase)) {
	// crack app by absolute path
	crackApp($inpCase);
} elseif (file_exists("/Applications/$inpCase")) {
	// crack app by app name
	crackApp("/Applications/$inpCase");
} elseif (file_exists("/Applications/$inpCase.app")) {
	// crack app by app name without extension
	crackApp("/Applications/$inpCase.app");
} else {
	// search by partial name
	$files = [];
	$dir = scandir('/Applications');
	foreach ($dir as $file) {
		if (substr($file, -4) === '.app' && strpos(strtolower($file), $inp) !== false)
			$files[] = $file;
	}
	
	$numfiles = count($files);
	switch ($numfiles) {
		case 0:
			echo 'No apps found';
			break;
		case 1:
			crackApp('/Applications/'.$files[0]);
			break;
		default:
			// found multiple apps
			echo "Found $numfiles apps:\n";
			for ($i = 0; $i < $numfiles; $i++)
				echo ($i + 1).'. '.$files[$i]."\n";
			
			echo 'Number: ';
			$fh = fopen ('php://stdin', 'r');
			$inp = (int)trim(fgets($fh));
			if ($inp >= 1 && $inp <= $numfiles)
				crackApp('/Applications/'.$files[$inp - 1]);
			else
				echo 'Invalid number, cancelled.';
	}
}

echo "\nFinished\n";

/**
 * Check if app is already hidden. If yes offer to show, if no offer to hide.
 * Write into Info.plist.
 */
function crackApp($appPath) {
	echo 'Cracking app '.basename($appPath, '.app')."\n";
	$infoFile = "$appPath/Contents/Info.plist";

	if (is_file($infoFile)) {
		$infoSrc = file_get_contents($infoFile);
		// search for the end of the plist/dictionaries
		if (preg_match('~</dict>\s*</plist>~', $infoSrc, $m)) {
			$crack = "<key>LSUIElement</key><true/>\n";
			// if not already hidden: inject
			if (!preg_match("~<key>LSUIElement</key>\s*(<string>1</string>|<true\s*/>)\n?~i", $infoSrc, $m2)) {
				$infoSrc = str_replace($m[0], $crack.$m[0], $infoSrc);
				injectInfo($appPath, $infoFile, $infoSrc);
			} else {
				echo "This app should be invisible in the Dock\nWould you like to show it? (y/n) ";
				$fh = fopen ('php://stdin', 'r');
				$inp = strtolower(trim(fgets($fh)));
				if ($inp == 'y') {
					$infoSrc = str_replace($m2[0], '', $infoSrc);
					injectInfo($appPath, $infoFile, $infoSrc);
				}
			}
		} else echo 'Couldn`t find </dict> and </plist> in Info.plist';
	} else echo 'Couldn`t find Info.plist';
}

/**
 * Write the new Info.plist and sign the app.
 */
function injectInfo($appPath, $infoFile, $infoSrc) {
	// backup Info.plist, just to be sure
	@copy($infoFile, '/tmp/'.basename($appPath).'-Info-'.date('Ymd-His').'.plist~backup');
	
	if (@file_put_contents($infoFile, $infoSrc)) {
		echo "Info.plist successfully injected\n";
		$codesign = "codesign -f -s - \"$appPath\"";
		echo "Signing ------\n";
		if (function_exists('exec')) {
			exec($codesign, $r);
			echo implode("\n", $r)."------\n";
		} else echo "Couldn`t sign, please execute the following line in your shell:\n$codesign";
	} else echo 'Info.plist couldn`t be written, missing sudo?';
}
