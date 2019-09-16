# FileStream

FileSream  is class that will enable thread safe file write & read. I am not the author of the the class, I just made some changes and change the protocol to `panx://`. The license is on the end of this page.

### Usage

The usage is easy, just add to filepath the `panx://` protocol.

For example:

```
file_put_contents("panx://text.txt", "Hello world!");
```



### License

See [safe-stream](https://github.com/nette/safe-stream) and [license on github](https://raw.githubusercontent.com/nette/safe-stream/master/license.md).

> New BSD License
>
> ​    \---------------
>
> 
>
> ​    Copyright (c) 2004, 2014 David Grudl (https://davidgrudl.com)
>
> ​    All rights reserved.
>
> 
>
> ​    Redistribution and use in source and binary forms, with or without modification,
>
> ​    are permitted provided that the following conditions are met:
>
> 
>
> ​        \* Redistributions of source code must retain the above copyright notice,
>
> ​        this list of conditions and the following disclaimer.
>
> 
>
> ​        \* Redistributions in binary form must reproduce the above copyright notice,
>
> ​        this list of conditions and the following disclaimer in the documentation
>
> ​        and/or other materials provided with the distribution.
>
> 
>
> ​        \* Neither the name of "Nette Framework" nor the names of its contributors
>
> ​        may be used to endorse or promote products derived from this software
>
> ​        without specific prior written permission.
>
> 
>
> ​    This software is provided by the copyright holders and contributors "as is" and
>
> ​    any express or implied warranties, including, but not limited to, the implied
>
> ​    warranties of merchantability and fitness for a particular purpose are
>
> ​    disclaimed. In no event shall the copyright owner or contributors be liable for
>
> ​    any direct, indirect, incidental, special, exemplary, or consequential damages
>
> ​    (including, but not limited to, procurement of substitute goods or services;
>
> ​    loss of use, data, or profits; or business interruption) however caused and on
>
> ​    any theory of liability, whether in contract, strict liability, or tort
>
> ​    (including negligence or otherwise) arising in any way out of the use of this
>
> ​    software, even if advised of the possibility of such damage.