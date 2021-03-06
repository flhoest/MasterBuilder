# Welcome to Challenge #1 of Master Builder Scripting Challenge!
So your Brik has been up and running for a while now and you sleep better at night. With the extra time on your hands, you decide to explore what’s possible with the Rubrik API. You’ve viewed the content available in Rubrik Build and feel inspired to get your hands dirty and build some code to get more information about your different VM workloads in Rubrik.

You have until Friday, March 8th to comment on this thread with your code.

Design points
* Code runs without errors
* Query for VMs
* Which SLAs are assigned to this object
* Get information about where (which cluster, host or physical location) this workload is running
* Output this information to the console

Extra Credit
* Get missed snapshots for these objects
* Modular design of your code/project
* Structured output, objects

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
php -f MasterBuilder_1.php
```

Have fun !

