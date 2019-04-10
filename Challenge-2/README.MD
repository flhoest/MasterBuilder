# Welcome to Challenge #2 of Master Builder Scripting Challenge!
Your Rubrik cluster has been successfully running with the built-in reporting capabilities. But now, your manager has requested that you try your hand at creating some customized reporting. The outline is simple – they want information on how workloads and backed up, by which SLAs, and which ones don’t have SLAs.

You’ll have to gather data on all the different SLAs on your cluster and take a look at what interesting data you can collect about your SLAs. Because this is a report, we’ll want you to present data that is actually actionable, so select relevant information to report upon.

You have until Friday, April 12th to comment on this thread with your code.
Design points
Code runs without errors
All backup workloads information
Where are they backed up (Rubrik/3rd party storage/Cloud/Cloud Conversion)
Which SLA assigned
Which have no SLA
SLA Information
Which SLAs are there
How many SLAs with no assigned objects
Usage/growth per SLA
Extra Credit
What other interesting data can you report on, be creative!
Different ways of displaying your results (xml/json/markdown)
Make it pretty, think of generated html, pdf, pptx, docx any kind of formatted

---

### How to run this script

First of all, it has been tested on MacOS and Linux environments, I have no idea on how to run this on Windows. You will need php-cli library installed (yum install php-cli -y). Next, 

* Create a folder to store all the required files;
* Transfer the MasterBuilder_1.php script;
* Transfer the Rubrik PHP Framwork that can be found [here](https://github.com/flhoest/Rubrik/blob/master/rkFramework.php)
* Create a file called rkCredentials.php with the following content : 

```php
<?PHP

// My cluster

	$clusterConnect=array(
		"username" => "my_username",
		"password" => "my_password",
		"ip" => "127.0.0.1",
	);

?>
```

Be sure to have a match with your cluster creds and IP.

Then simply run the script like this : 

```
php -f MasterBuilder_2.php
```

Have fun !
