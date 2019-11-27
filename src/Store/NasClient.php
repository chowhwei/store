<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use DirectoryIterator;
use ErrorException;
use Exception;
use SplFileInfo;
use UnexpectedValueException;

class NasClient implements StoreClient
{
    /** @var string $root */
    protected $root;
    /** @var string $dir */
    protected $dir;
    /** @var SplFileInfo $spi */
    protected $spi;

    /**
     * FileClient constructor.
     * @param array $config
     * @param string $dir
     * @throws Exception
     */
    public function __construct(array $config, string $dir)
    {
        $this->root = $config['root'];
        $this->dir = $dir;
        $this->checkDir();
    }

    /**
     * @throws Exception
     */
    protected function checkDir()
    {
        $root = $this->root . DIRECTORY_SEPARATOR . $this->dir;

        try {
            $this->spi = new SplFileInfo($root);
        } catch (UnexpectedValueException $e) {
            $this->spi = $this->makeDirectory($root, 0777, TRUE);
        }

        if ($this->spi->isFile()) {
            throw new Exception(strtr('Unable to create directory as a file already exists : :resource', array(':resource' => $this->spi->getRealPath())));
        }

        if (!$this->spi->isReadable()) {
            throw new Exception(strtr('Unable to read from the directory :resource', array(':resource' => $this->spi->getRealPath())));
        }

        if (!$this->spi->isWritable()) {
            throw new Exception(strtr('Unable to write to the directory :resource', array(':resource' => $this->spi->getRealPath())));
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
            throw new Exception(strtr('Failed to create the defined directory : :directory', [':directory' => $directory]));
        }
        chmod($directory, $mode);

        return new SplFileInfo($directory);
    }

    /**
     * @param string $id
     * @param null $default
     * @return mixed
     * @throws Exception
     */
    public function get(string $id, $default = null)
    {
        $filename = $this->getFilename($id);
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
        return hash('sha256', $string);
    }

    protected function resolveDirectory($filename)
    {
        return $this->spi->getRealPath() . DIRECTORY_SEPARATOR
            . $filename[0] . $filename[1] . DIRECTORY_SEPARATOR
            . $filename[2] . $filename[3] . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $id
     * @param mixed $data
     * @return bool
     * @throws Exception
     * @throws ErrorException
     */
    public function set(string $id, $data): bool
    {
        $filename = $this->getFilename($id);
        $directory = $this->resolveDirectory($filename);

        $dir = new SplFileInfo($directory);

        if (!$dir->isDir()) {
            if (!mkdir($directory, 0777, TRUE)) {
                throw new Exception(strtr(__METHOD__ . ' unable to create directory : :directory', [':directory' => $directory]));
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
    public function del(string $id): bool
    {
        $filename = $this->getFilename($id);
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
                        throw new Exception(strtr(__METHOD__ . ' failed to delete file : :file', [':file' => $file->getRealPath()]));
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
                        throw new Exception(strtr(__METHOD__ . ' failed to delete directory : :directory', [':directory' => $file->getRealPath()]));
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