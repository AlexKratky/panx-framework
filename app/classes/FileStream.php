<?php
/**
 * @name FileStream.php
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @description Thread safe file write & read.
 * @see https://github.com/nette/safe-stream
 * @license https://raw.githubusercontent.com/nette/safe-stream/master/license.md

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
	/** Name of stream protocol - panx:// */
	public const PROTOCOL = 'panx';

	/** @var resource  orignal file handle */
	private $handle;

	/** @var resource|null  temporary file handle */
	private $tempHandle;

	/** @var string  orignal file path */
	private $file;

	/** @var string  temporary file path */
	private $tempFile;

	/** @var bool */
	private $deleteFile;

	/** @var bool  error detected? */
	private $writeError = false;


	/**
	 * Registers protocol 'panx://'.
	 */
	public static function init(): bool
	{
		foreach (array_intersect(stream_get_wrappers(), ['safe', self::PROTOCOL]) as $name) {
			stream_wrapper_unregister($name);
		}
		stream_wrapper_register('safe', __CLASS__); // old protocol
		return stream_wrapper_register(self::PROTOCOL, __CLASS__);
	}


	/**
	 * Opens file.
	 */
	public function stream_open(string $path, string $mode, int $options): bool
	{
		$path = substr($path, strpos($path, ':') + 3);  // trim protocol panx://

		$flag = trim($mode, 'crwax+');  // text | binary mode
		$mode = trim($mode, 'tb');     // mode
		$use_path = (bool) (STREAM_USE_PATH & $options); // use include_path?
		// open file
		if ($mode === 'r') { // provides only isolation
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
			if ($this->checkAndLock($this->handle = @fopen($path, 'x' . $flag, $use_path), LOCK_EX)) { // intentionally @
				$this->deleteFile = true;

			} elseif (!$this->checkAndLock($this->handle = fopen($path, 'a+' . $flag, $use_path), LOCK_EX)) {
				return false;
			}

		} else {
			trigger_error("Unknown mode $mode", E_USER_WARNING);
			return false;
		}

		// create temporary file in the same directory to provide atomicity
		$tmp = '~~' . lcg_value() . '.tmp';
		if (!$this->tempHandle = fopen($path . $tmp, (strpos($mode, '+') ? 'x+' : 'x') . $flag, $use_path)) {
			$this->clean();
			return false;
		}
		$this->tempFile = realpath($path . $tmp);
		$this->file = substr($this->tempFile, 0, -strlen($tmp));

		// copy to temporary file
		if ($mode === 'r+' || $mode[0] === 'a' || $mode[0] === 'c') {
			$stat = fstat($this->handle);
			fseek($this->handle, 0);
			if (stream_copy_to_stream($this->handle, $this->tempHandle) !== $stat['size']) {
				$this->clean();
				return false;
			}

			if ($mode[0] === 'a') { // emulate append mode
				fseek($this->tempHandle, 0, SEEK_END);
			}
		}

		return true;
	}


	/**
	 * Checks handle and locks file.
	 */
	private function checkAndLock($handle, int $lock): bool
	{
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
	private function clean(): void
	{
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
	 * Closes file.
	 */
	public function stream_close(): void
	{
		if (!$this->tempFile) { // 'r' mode
			flock($this->tempHandle, LOCK_UN);
			fclose($this->tempHandle);
			return;
		}

		flock($this->handle, LOCK_UN);
		fclose($this->handle);
		fclose($this->tempHandle);

		if ($this->writeError || !rename($this->tempFile, $this->file)) { // try to rename temp file
			unlink($this->tempFile); // otherwise delete temp file
			if ($this->deleteFile) {
				unlink($this->file);
			}
		}
	}


	/**
	 * Reads up to length bytes from the file.
	 */
	public function stream_read(int $length)
	{
		return fread($this->tempHandle, $length);
	}


	/**
	 * Writes the string to the file.
	 */
	public function stream_write(string $data)
	{
		$len = strlen($data);
		$res = fwrite($this->tempHandle, $data, $len);

		if ($res !== $len) { // disk full?
			$this->writeError = true;
		}

		return $res;
	}


	/**
	 * Truncates a file to a given length.
	 */
	public function stream_truncate(int $size): bool
	{
		return ftruncate($this->tempHandle, $size);
	}


	/**
	 * Returns the position of the file.
	 */
	public function stream_tell(): int
	{
		return ftell($this->tempHandle);
	}


	/**
	 * Returns true if the file pointer is at end-of-file.
	 */
	public function stream_eof(): bool
	{
		return feof($this->tempHandle);
	}


	/**
	 * Sets the file position indicator for the file.
	 */
	public function stream_seek(int $offset, int $whence = SEEK_SET): bool
	{
		return fseek($this->tempHandle, $offset, $whence) === 0;
	}


	/**
	 * Gets information about a file referenced by $this->tempHandle.
	 */
	public function stream_stat()
	{
		return fstat($this->tempHandle);
	}


	/**
	 * Gets information about a file referenced by filename.
	 */
	public function url_stat(string $path, int $flags)
	{
		// This is not thread safe
		$path = substr($path, strpos($path, ':') + 3);
		return ($flags & STREAM_URL_STAT_LINK) ? @lstat($path) : @stat($path); // intentionally @
	}


	/**
	 * Deletes a file.
	 * On Windows unlink is not allowed till file is opened
	 */
	public function unlink(string $path): bool
	{
		$path = substr($path, strpos($path, ':') + 3);
		return unlink($path);
	}

	public function stream_lock(int $operation): bool {
		return true;
	}
}