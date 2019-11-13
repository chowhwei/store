<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use DirectoryIterator;
use ErrorException;
use Exception;
use SplFileInfo;
use UnexpectedValueException;

class FileClient implements StoreClient
{
    /** @var string $nfs_root */
    protected $nfs_root;
    /** @var string $app */
    protected $app;
    /** @var SplFileInfo $dir */
    protected $dir;

    /**
     * FileClient constructor.
     * @param string $nfs_root
     * @param string $app
     * @throws Exception
     */
    public function __construct(string $nfs_root, string $app)
    {
        $this->nfs_root = $nfs_root;
        $this->app = $app;
        $this->checkDir();
    }

    /**
     * @throws Exception
     */
    protected function checkDir()
    {
        $root = $this->nfs_root . DIRECTORY_SEPARATOR . $this->app;

        try {
            $this->dir = new SplFileInfo($root);
        } catch (UnexpectedValueException $e) {
            $this->dir = $this->makeDirectory($root, 0777, TRUE);
        }

        if ($this->dir->isFile()) {
            throw new Exception('Unable to create directory as a file already exists : :resource', array(':resource' => $this->dir->getRealPath()));
        }

        if (!$this->dir->isReadable()) {
            throw new Exception('Unable to read from the directory :resource', array(':resource' => $this->dir->getRealPath()));
        }

        if (!$this->dir->isWritable()) {
            throw new Exception('Unable to write to the directory :resource', array(':resource' => $this->dir->getRealPath()));
        }
    }

    /**
     * @param $directory
     * @param int $mode
     * @param bool $recursive
     * @param resource $context
     * @return SplFileInfo
     * @throws Exception
     */
    protected function makeDirectory(string $directory, int $mode = 0777, bool $recursive = FALSE, $context = NULL)
    {
        if (!mkdir($directory, $mode, $recursive, $context)) {
            throw new Exception('Failed to create the defined directory : :directory', [':directory' => $directory]);
        }
        chmod($directory, $mode);

        return new SplFileInfo($directory);
    }

    /**
     * @param string $id
     * @param null $default
     * @return string
     * @throws Exception
     */
    public function get($id, $default = null): string
    {
        $filename = $this->getFilename($this->getSanitizedId($id));
        $directory = $this->resolveDirectory($filename);

        try {
            $file = new SplFileInfo($directory . $filename);

            if (!$file->isFile()) {
                return $default;
            } else {
                $data = $file->openFile();
                $dt = $data->fgets();   //第一行是日期时间

                if ($data->eof()) {
                    throw new Exception(__METHOD__ . ' corrupted file!');
                }

                $content = '';

                while ($data->eof() === FALSE) {
                    $content .= $data->fgets();
                }

                return unserialize($content);
            }

        } catch (Exception $e) {
            // Handle ErrorException caused by failed unserialization
            if ($e->getCode() === E_NOTICE) {
                throw new Exception(__METHOD__ . ' failed to unserialize object with message : ' . $e->getMessage());
            }

            // Otherwise throw the exception
            throw $e;
        }
    }

    protected function getFilename($string)
    {
        return sha1($string);
    }

    protected function getSanitizedId($id)
    {
        // Change slashes and spaces to underscores
        return str_replace(array('/', '\\', ' '), '_', $id);
    }

    protected function resolveDirectory($filename)
    {

        return $this->dir->getRealPath() . DIRECTORY_SEPARATOR
            . $filename[0] . DIRECTORY_SEPARATOR
            . $filename[1] . $filename[2] . DIRECTORY_SEPARATOR
            . $filename[3] . $filename[4] . $filename[5] . DIRECTORY_SEPARATOR
            . $filename[6] . $filename[7] . $filename[8] . $filename[9] . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $id
     * @param mixed $data
     * @return bool
     * @throws Exception
     * @throws ErrorException
     */
    public function set($id, $data): bool
    {
        $filename = $this->getFilename($this->getSanitizedId($id));
        $directory = $this->resolveDirectory($filename);

        $dir = new SplFileInfo($directory);

        if (!$dir->isDir()) {
            if (!mkdir($directory, 0777, TRUE)) {
                throw new Exception(__METHOD__ . ' unable to create directory : :directory', [':directory' => $directory]);
            }

            chmod($directory, 0777);
            $up = 0;
            $updir = $directory;
            while ($up < 3) {
                $updir = dirname($updir);
                if (!(fileperms($updir) & 0x0002)) {
                    chmod($updir, 0777);
                }
                $up++;
            }
        }

        $resource = new SplFileInfo($directory . $filename);
        $file = $resource->openFile('w');
        if (!(fileperms($directory . $filename) & 0x0002)) {
            chmod($directory . $filename, 0777);
        }

        try {
            $dt = date('Y-m-d H:i:s');
            $data = $dt . "\n" . serialize($data);
            $file->fwrite($data, strlen($data));
            return (bool)$file->fflush();
        } catch (ErrorException $e) {
            // If serialize through an error exception
            if ($e->getCode() === E_NOTICE) {
                // Throw a caching error
                throw new Exception(__METHOD__ . ' failed to serialize data for storing with message : ' . $e->getMessage());
            }

            // Else rethrow the error exception
            throw $e;
        }
    }

    /**
     * @param string $id
     * @return bool
     * @throws Exception
     */
    public function del($id): bool
    {
        $filename = $this->getFilename($this->getSanitizedId($id));
        $directory = $this->resolveDirectory($filename);

        return $this->deleteFile(new SplFileInfo($directory . $filename), NULL, TRUE);
    }

    /**
     * @param SplFileInfo $file
     * @param bool $retain_parent_directory
     * @param bool $ignore_errors
     * @param bool $only_expired
     * @return bool
     * @throws Exception
     */
    protected function deleteFile(SplFileInfo $file, $retain_parent_directory = FALSE, $ignore_errors = FALSE)
    {
        // Allow graceful error handling
        try {
            // If is file
            if ($file->isFile()) {
                try {
                    return unlink($file->getRealPath());
                } catch (Exception $e) {
                    if ($e->getCode() === E_WARNING) {
                        throw new Exception(__METHOD__ . ' failed to delete file : :file', [':file' => $file->getRealPath()]);
                    }
                }
            } // Else, is directory
            elseif ($file->isDir()) {
                $files = new DirectoryIterator($file->getPathname());

                while ($files->valid()) {
                    $name = $files->getFilename();

                    // If the name is not a dot
                    if ($name != '.' AND $name != '..') {
                        // Create new file resource
                        $fp = new SplFileInfo($files->getRealPath());
                        // Delete the file
                        $this->deleteFile($fp);
                    }

                    // Move the file pointer on
                    $files->next();
                }

                // If set to retain parent directory, return now
                if ($retain_parent_directory) {
                    return TRUE;
                }

                try {
                    // Remove the files iterator
                    // (fixes Windows PHP which has permission issues with open iterators)
                    unset($files);

                    // Try to remove the parent directory
                    return rmdir($file->getRealPath());
                } catch (Exception $e) {
                    // Catch any delete directory warnings
                    if ($e->getCode() === E_WARNING) {
                        throw new Exception(__METHOD__ . ' failed to delete directory : :directory', [':directory' => $file->getRealPath()]);
                    }
                    throw $e;
                }
            } else {
                // We get here if a file has already been deleted
                return FALSE;
            }
        } // Catch all exceptions
        catch (Exception $e) {
            // If ignore_errors is on
            if ($ignore_errors === TRUE) {
                // Return
                return FALSE;
            }
            // Throw exception
            throw $e;
        }
    }
}