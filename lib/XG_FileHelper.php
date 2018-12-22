<?php

/**
 * Useful functions for working with files and uploads.
 */
class XG_FileHelper {

    /**
     * Creates (and saves) an UploadedFile content object.
     *
     * @param $postVariableName string  The name of the file field containing the uploaded file.
     * @param $forcePrivate boolean optional Force the content object to be private? Defaults to false
     * @return array  The UploadedFile content object, the filename (without path), the file size (in bytes), and the file's MIME type.
     */
    public static function createUploadedFileObject($postVariableName, $forcePrivate = false) {
        $file = XN_Content::create('UploadedFile');
        $file->isPrivate = $forcePrivate || XG_App::appIsPrivate();
        $file->my->mozzle = W_Cache::current('W_Widget')->dir;
        $file->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        $file->save();
        return array($file, self::basename($_POST[$postVariableName]), $_POST[$postVariableName . ':size'], $_POST[$postVariableName] . ':type');
    }


    /**
     * Determine whether an uploaded POST object is a valid image type
     *
     * @param string $postVariableName  The name of the file field containing the uploaded file.
     * @param array $additionalMimeTypes an array of valid image types; optional.  If not supplied, a commone set will be used.
     * @return boolean  Whether the $_POST object is a valid image type
     */
    public static function isValidImageType($postVariableName, $validTypes = null) {
        if (!is_array($validTypes)) {
            $validTypes = array('image/jpeg','image/pjpeg','image/gif','image/png','image/x-png','image/bmp');
        }
        if ($_POST[$postVariableName] && $_POST[$postVariableName . ':status'] != 0) {
            return false;
        }
        if ($_POST[$postVariableName] && ! in_array($_POST[$postVariableName . ':type'], $validTypes)) {
            return false;
        }
        return true;
    }

    /**
     * Removes the path from the given filename. Works for both Windows- and Unix-style paths.
     *
     * @param $filename string  A filename, which may be qualified with a path.
     * @return string  The filename with path removed.
     */
    public static function basename($filename) {
        $filename = basename($filename);
        $lastBackslash = mb_strrpos($filename, '\\');
        if (($lastBackslash !== false) && ($lastBackslash < (mb_strlen($filename) -1 ))) {
            $filename = mb_substr($filename, $lastBackslash + 1);
        }
        return $filename;
    }

    /**
     * Returns a description of the error indicated by the given status number.
     *
     * @param $status integer  The status number of the upload error.
     * @return string  An error message.
     * @see "Handling Uploaded Files", http://documentation.ning.com/post.php?Post:slug=FileUpload
     */
    public static function uploadErrorMessage($status) {
        if ($status == 1) { return xg_text('FILE_EXCEEDED_MAXIMUM_SIZE'); }
        if ($status == 2) { return xg_text('FILE_EXCEEDED_MAXIMUM_SIZE'); }
        if ($status == 3) { return xg_text('PART_OF_FILE_WAS_UPLOADED'); }
        if ($status == 4) { return xg_text('NO_FILE_WAS_UPLOADED'); }
        return xg_text('PROBLEM_OCCURRED_DURING_UPLOAD');
    }

    /**
     * Adds height and width parameters to an image's API URL and optionally
     *   constrains either or both of those parameters maintaining aspect ratio
     *
     * @param $imageUrl string an image's API URL ('http://api.ning.com/files/...')
     * @param $maxWidth integer (Optional) The maximum width to which to
     * 		constrain the image
     * @param $maxHeight integer (Optional) The maximum height
     * @return string a modified URL with dimensions as specified
     */
    public static function setImageUrlDimensions($imageUrl, $maxWidth = NULL, $maxHeight = NULL) {
        //  Get image dimensions.  Retrieve the actual image if necessary.
        $width = $height = NULL;
        if (preg_match('@\Wwidth=(\d+)@u', $imageUrl, $matches)) {
            $width = $matches[1];
        }
        if (preg_match('@\Wheight=(\d+)@u', $imageUrl, $matches)) {
            $height = $matches[1];
        }

        if ($width === NULL || $height === NULL) {
            //  Get the actual dimensions by retrieving the image
            $imageInfo = getimagesize($imageUrl);
            if (! $imageInfo) {
                return $imageUrl;
            }
            $width = $imageInfo[0];
            $height = $imageInfo[1];
        }

        //  Now constrain the image dimensions if requested and necessary
        if (($maxWidth && $width > $maxWidth) || ($maxHeight && $height > $maxHeight)) {
            $ratio = (float) $width / (float) $height;
            if ($maxWidth) {
                $widthRatio = (float) $width / (float) $maxWidth;
            }
            if ($maxHeight) {
                $heightRatio = (float) $height / (float) $maxHeight;
            }
            //  Scale by the larger factor (the greater reduction)
            $scale = max($widthRatio, $heightRatio);
            $width = floor($width / $scale);
            $height = floor($height / $scale);
        }
        $imageUrl = XG_HttpHelper::addParameter($imageUrl, 'width', $width);
        $imageUrl = XG_HttpHelper::addParameter($imageUrl, 'height', $height);
        return $imageUrl;
    }

    /**
     * Writes a string to a file, just like file_put_contents(), but also saves rolling backups
     * to /xn_private/xn_volatile/backups. For example, foo.txt will have backups named
     * foo.txt.1, foo.txt.2, foo.txt.3.
     *
     * @param $filename string  the path of the file to save
     * @param $data string  the data to put into the file
     * @param $rollingBackupCount int  the number of backups to keep
     */
    public static function filePutContentsWithBackup($filename, $data, $rollingBackupCount = 5) {
        file_put_contents($filename, $data);
        @mkdir(NF_APP_BASE . '/xn_private/xn_volatile/backups', 0777, true);
        $oldestFile = '';
        $oldestFileTime = null;
        for ($i = 1; $i <= $rollingBackupCount; $i++) {
            $backupFilename = NF_APP_BASE . '/xn_private/xn_volatile/backups/' . basename($filename) . '.' . $i;
            if (file_exists($backupFilename)) {
                if (is_null($oldestFileTime) || filemtime($backupFilename) < $oldestFileTime) {
                    $oldestFile = $backupFilename;
                    $oldestFileTime = filemtime($backupFilename);
                }
            } else {
                file_put_contents($backupFilename, $data);
                return;
            }
        }
        file_put_contents($oldestFile, $data);
    }

    /**
     * Remove old files that match certain naming and metadata conditions
     *
     * @param $matchPattern string|array As a string, this parameter is treated as
     *   a glob pattern to find eligible files. As an array, the 'glob' element
     *   is used to find eligible files, and the 'regex' element is used to further
     *   narrow the list of eligible files
     * @param $conditions optional array of conditions that qualify which files to remove
     *     max-count: integer number of maximum files to leave (oldest are removed)
     *     max-age: remove files more than this many seconds old
     *     min-count: whatever else is going on, ensure that at least this many files are left
     *   If no conditions are specified, all files that $matchPattern finds are removed
     * @return array of filenames that were removed
     */
    public static function fileCleanup($matchPattern, $conditions = null) {
        /* Step 1: glob to find eligible files */
        if (is_array($matchPattern)) {
            if (isset($matchPattern['glob'])) {
                $files = glob($matchPattern['glob']);
            } else {
                throw new XN_IllegalArgumentException("No 'glob' element specified to XG_FileHelper::fileCleanup()");
            }
            /* If a regex was specified, use it for further filtering */
            if (isset($matchPattern['regex'])) {
                $files = preg_grep($matchPattern['regex'], $files);
            }
        }
        else {
            $files = glob($matchPattern);
        }
        /* Step 2: use the $conditions to determine what to remove */
        /* If no conditions are specified, remove everything that matched Step 1 */
        if (is_null($conditions) || (is_array($conditions) && (count($conditions) == 0))) {
            $toRemove = $files;
        }
        else if (is_array($conditions)) {
            /* For conditions this function currently understands (max-count, max-age),
             * we need mtime for all the files. If the list of conditions changes/expands,
             * we may need to gather full stat information for the files
             */
            $files = array_combine($files, array_map('filemtime', $files));
            /* Sort the files are sorted newest first */
            arsort($files);

            /* A temporary array where we can store the files to remove as keys. This
             * helps with overlaps between the different conditions */
            $tmp = array();

            /* If there are more files on the list than we want to keep, add all of the
             * files older than the ones we want to keep to the remove list */
            if (isset($conditions['max-count'])) {
                if (count($files) > $conditions['max-count']) {
                    $extras =array_slice($files, $conditions['max-count'], count($files), true);
                    foreach ($extras as $file => $mtime) {
                        $tmp[$file] = $mtime;
                    }
                }
            }

            /* If there are files older than the max age on the list, add them to the
             * remove list */
            if (isset($conditions['max-age'])) {
                $minMtime = time() - $conditions['max-age'];
                foreach ($files as $file => $mtime) {
                    if ($mtime < $minMtime) {
                        $tmp[$file] = $mtime;
                    }
                }
            }

            /* Put tmp in a predictable order (newest first) */
            arsort($tmp);

           /* Make sure we don't remove too many files -- this should go
             * after all other conditions so that anything that would be
             * put into $tmp is already there */
            if (isset($conditions['min-count'])) {
                /* If no other conditions were specified, then include all of $files in $tmp */
                if (count($conditions) == 1) {
                    $tmp = $files;
                    arsort($tmp);
                }
                $remainingFileCount = count($files) - count($tmp);
                /* Too few files? Add some back */
                if ($remainingFileCount < $conditions['min-count']) {
                    $toKeepFileCount = $conditions['min-count'] - $remainingFileCount;
                    /* Remove the first $toKeepFileCount entries (the newest) from $tmp */
                    $tmp = array_slice($tmp, $toKeepFileCount, count($tmp), true);
                }
            }

            /* Construct the list of files to remove */
            $toRemove = array_keys($tmp);
        }

        /* Step 3: Actually remove the specified files */
        foreach ($toRemove as $file) {
            unlink($file);
        }
        return $toRemove;
    }

    /**
     * Deletes the directory, its subdirectories, and their files.
     *
     * @param $dir string  the directory path
     */
    public static function deltree($dir) {
        // Based on code by davedx@gmail.com, http://php.net/manual/en/function.rmdir.php  [Jon Aquino 2008-04-19]
        if (! is_dir($dir)) { throw new Exception('Not a directory (576150705)'); }
        foreach(glob($dir.'/*') as $child) {
            if (is_dir($child) && !is_link($child)) {
                self::deltree($child);
            } else {
                unlink($child);
            }
        }
        rmdir($dir);
    }
}
