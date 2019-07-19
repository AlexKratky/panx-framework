# Mail class

Mail class is very simple class to sending mail using PHP mail() function. The reason why it is class is you can easy implement some mail clients using the Mail class

The class have following functions:

* subject($s) - Sets the subject to $s.
* message($m) - Sets the message to $m
* header($h) - Sets the headers to $h
* send($reciever) - Send email to $reciever with specified subject, message and headers.