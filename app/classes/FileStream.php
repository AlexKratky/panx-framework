<?php
/**
 * @name FileStream.php
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @description Thread safe file write & read.
 * @see https://github.com/nette/safe-stream
 * @license https: //raw.githubusercontent.com/nette/safe-stream/master/license.md

 	New BSD License
	---------------

	Copyright (c) 2004, 2014 David Grudl (https://davidgrudl.com)
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification,
	are permitted provided that the following conditions are met:

		* Redistributions of source code must retain the above copyright notice,
		this list of conditions and the following disclaimer.

		* Redistributions in binary form must reproduce the above copyright notice,
		this list of conditions and the following disclaimer in the documentation
		and/or other materials provided with the distribution.

		* Neither the name of "Nette Framework" nor the names of its contributors
		may be used to endorse or promote products derived from this software
		without specific prior written permission.

	This software is provided by the copyright holders and contributors "as is" and
	any express or implied warranties, including, but not limited to, the implied
	warranties of merchantability and fitness for a particular purpose are
	disclaimed. In no event shall the copyright owner or contributors be liable for
	any direct, indirect, incidental, special, exemplary, or consequential damages
	(including, but not limited to, procurement of substitute goods or services;
	loss of use, data, or profits; or business interruption) however caused and on
	any theory of liability, whether in contract, strict liability, or tort
	(including negligence or otherwise) arising in any way out of the use of this
	software, even if advised of the possibility of such damage.

 */

declare(strict_types=1);
class FileStream
{
	/** 
     * The name of file stream protocol (panx://*).
    */
	public const PROTOCOL = 'panx';
	/** 
     * @var resource The orignal file handle.
    */
	private $handle;
	/** 
     * @var resource The temporary file handle.
    */
	private $tempHandle;
	/**
     * @var string The file path.
    */
	private $file;
	/** 
     * @var string The temporary file path.
    */
	private $tempFile;
    /** 
     * @var bool 
    */
	private $deleteFile;
	/** 
     * @var bool error detected? 
    */
    private $writeError = false;
    
    /**
	 * Registers file stream protocol (FileStrean::Protocol).
	 */
	public static function init(): bool {
		foreach (array_intersect(stream_get_wrappers(), ['safe', self::PROTOCOL]) as $name) {
			stream_wrapper_unregister($name);
		}
		return stream_wrapper_register(self::PROTOCOL, __CLASS__);
	}

    /**
	 * Opens file or URL.
     * @param string $path Specifies the URL that was passed to the original function.
     * @param string $mode The mode used to open the file, as detailed for fopen().
     * @param int $options Holds additional flags set by the streams API. It can hold one or more values OR'd together.
     * @param string $opened_path If the path is opened successfully, and STREAM_USE_PATH is set in options, opened_path should be set to the full path of the file/resource that was actually opened.
     * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool {
		$path = substr($path, strpos($path, ':') + 3);
		$flag = trim($mode, 'crwax+');
		$mode = trim($mode, 'tb');
		$use_path = (bool) (STREAM_USE_PATH & $options);
		if ($mode === 'r') {
			return $this->checkAndLock($this->tempHandle = fopen($path, 'r' . $flag, $use_path), LOCK_SH);
		} elseif ($mode === 'r+') {
			if (!$this->checkAndLock($this->handle = fopen($path, 'r' . $flag, $use_path), LOCK_EX)) {
				return false;
			}
		} elseif ($mode[0] === 'x') {
			if (!$this->checkAndLock($this->handle = fopen($path, 'x' . $flag, $use_path), LOCK_EX)) {
				return false;
			}
			$this->deleteFile = true;
		} elseif ($mode[0] === 'w' || $mode[0] === 'a' || $mode[0] === 'c') {
			if ($this->checkAndLock($this->handle = @fopen($path, 'x' . $flag, $use_path), LOCK_EX)) {
				$this->deleteFile = true;
			} elseif (!$this->checkAndLock($this->handle = fopen($path, 'a+' . $flag, $use_path), LOCK_EX)) {
				return false;
			}
		} else {
			trigger_error("Unknown mode $mode", E_USER_WARNING);
			return false;
		}
		$tmp = '~~' . lcg_value() . '.tmp';
		if (!$this->tempHandle = fopen($path . $tmp, (strpos($mode, '+') ? 'x+' : 'x') . $flag, $use_path)) {
			$this->clean();
			return false;
		}
		$this->tempFile = realpath($path . $tmp);
		$this->file = substr($this->tempFile, 0, -strlen($tmp));
		if ($mode === 'r+' || $mode[0] === 'a' || $mode[0] === 'c') {
			$stat = fstat($this->handle);
			fseek($this->handle, 0);
			if (stream_copy_to_stream($this->handle, $this->tempHandle) !== $stat['size']) {
				$this->clean();
				return false;
			}
			if ($mode[0] === 'a') {
				fseek($this->tempHandle, 0, SEEK_END);
			}
		}
		return true;
    }
    
	/**
	 * Checks handle and locks file.
	 */
	private function checkAndLock($handle, int $lock): bool {
		if (!$handle) {
			return false;
		} elseif (!flock($handle, $lock)) {
			fclose($handle);
			return false;
		}
		return true;
    }
    
	/**
	 * Error destructor.
	 */
	private function clean(): void {
		flock($this->handle, LOCK_UN);
		fclose($this->handle);
		if ($this->deleteFile) {
			unlink($this->file);
		}
		if ($this->tempHandle) {
			fclose($this->tempHandle);
			unlink($this->tempFile);
		}
    }
    
	/**
	 * Close a resource.
	 */
	public function stream_close(): void {
		if (!$this->tempFile) {
			flock($this->tempHandle, LOCK_UN);
			fclose($this->tempHandle);
			return;
		}
		flock($this->handle, LOCK_UN);
		fclose($this->handle);
		fclose($this->tempHandle);
		if ($this->writeError || !rename($this->tempFile, $this->file)) {
			unlink($this->tempFile); 
			if ($this->deleteFile) {
				unlink($this->file);
			}
		}
    }
    
	/**
	 * Read from stream.
     * @param int $count How many bytes of data from the current position should be returned.
     * @return string If there are less than count bytes available, return as many as are available. If no more data is available, return either FALSE or an empty string.
	 */
	public function stream_read(int $count) {
		return fread($this->tempHandle, $count);
    }
    
	/**
	 * Write to stream.
     * @param string $data Should be stored into the underlying stream.
     * @return int Should return the number of bytes that were successfully stored, or 0 if none could be stored.
	 */
	public function stream_write(string $data): int {
		$len = strlen($data);
		$res = fwrite($this->tempHandle, $data, $len);
		if ($res !== $len) {
			$this->writeError = true;
		}
		return $res;
    }
    
	/**
	 * Truncate stream.
     * @param int $new_size The new size.
     * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function stream_truncate(int $new_size): bool {
		return ftruncate($this->tempHandle, $new_size);
    }
    
	/**
	 * Retrieve the current position of a stream.
     * @return int Should return the current position of the stream.
	 */
	public function stream_tell(): int {
		return ftell($this->tempHandle);
    }
    
	/**
	 * Tests for end-of-file on a file pointer.
     * @return bool Should return TRUE if the read/write position is at the end of the stream and if no more data is available to be read, or FALSE otherwise.
	 */
	public function stream_eof(): bool {
		return feof($this->tempHandle);
    }
    
	/**
	 * Seeks to specific location in a stream.
     * @param int $offset The stream offset to seek to.
     * @param int $whence Possible values:
     *                          • SEEK_SET - Set position equal to offset bytes.
     *                          • SEEK_CUR - Set position to current location plus offset.
     *                          • SEEK_END - Set position to end-of-file plus offset.
     * @return bool Return TRUE if the position was updated, FALSE otherwise.
	 */
	public function stream_seek(int $offset, int $whence = SEEK_SET): bool {
		return fseek($this->tempHandle, $offset, $whence) === 0;
    }
    
	/**
	 * Retrieve information about a file resource
     * @return array See https://www.php.net/manual/en/function.stat.php
	 */
	public function stream_stat() {
		return fstat($this->tempHandle);
    }
    
	/**
	 * Retrieve information about a file.
     * @param string $path The file path or URL to stat. Note that in the case of a URL, it must be a :// delimited URL. Other URL forms are not supported.
     * @param int $flags Holds additional flags set by the streams API. It can hold one or more values OR'd together.
     * @return array Should return as many elements as stat() does. Unknown or unavailable values should be set to a rational value (usually 0).
	 */
	public function url_stat(string $path, int $flags) {
		$path = substr($path, strpos($path, ':') + 3);
		return ($flags & STREAM_URL_STAT_LINK) ? @lstat($path) : @stat($path);
    }
    
	/**
	 * Delete a file.
     * @param string $path The file URL which should be deleted.
     * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function unlink(string $path): bool {
		$path = substr($path, strpos($path, ':') + 3);
		return unlink($path);
	}
}