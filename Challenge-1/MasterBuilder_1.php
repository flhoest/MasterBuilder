<?php
	
	/*				__________        ___.            .__  __    
					\______   \ __ __ \_ |__  _______ |__||  | __
					 |       _/|  |  \ | __ \ \_  __ \|  ||  |/ /
					 |    |   \|  |  / | \_\ \ |  | \/|  ||    < 
					 |____|_  /|____/  |___  / |__|   |__||__|_ \
						\/             \/                  \/	
	*/
	
	// Master Challenge #1

	include_once "rkCredentials.php";
	include_once "rkFramework.php";

	$CSVFileName='output.csv';
	date_default_timezone_set('UTC');
	$myVMs=array();


	// =========================================================================================================================================
	// Step 1 : Get all VMs (I do not have Hyper-V infrastructure, so this section is probably not bug free....)
	// =========================================================================================================================================
	
	// Start the clock
    	$time1=time();
		
	$totalVM=0;
	system('clear');
	print("Collecting data ...");
	
	$vmwareVMs=rkGetvmwareVM($clusterConnect);
	$nutanixVMs=rkGetNutanixVM($clusterConnect);
	$hypervVMs=rkGetHypervVM($clusterConnect);
	
	print(" Done!\n");

	print("Computing data (one \"dot\" per vmware VM analysed - Total is : ".rkColorOutput(count($vmwareVMs->data)).") : ");

	for($i=0;$i<count($vmwareVMs->data);$i++)
	{
		$myVMs[$totalVM]["host"]=$vmwareVMs->data[$i]->hostName;
		$myVMs[$totalVM]["vmname"]=$vmwareVMs->data[$i]->name;

		// In my environment, below call is very slow, I wonder why?!?
		$missed=rkGetFailedAmount($clusterConnect,$vmwareVMs->data[$i]->name);
		$tmpCount=count($missed->data);
		$myVMs[$totalVM]["missed"]=$tmpCount;
		$myVMs[$totalVM]["sla"]=$vmwareVMs->data[$i]->effectiveSlaDomainName;
		
		$totalVM++;
		print(".");
	}

	print(" Done!\n");

	print("Computing data (one \"dot\" per Nutanix VM analysed - Total is : ".rkColorOutput(count($nutanixVMs->data)).") : ");

	for($i=0;$i<count($nutanixVMs->data);$i++)
	{
		$myVMs[$totalVM]["host"]=$nutanixVMs->data[$i]->nutanixClusterName;
		$myVMs[$totalVM]["vmname"]=$nutanixVMs->data[$i]->name;

		// In my environment, below call is very slow, I wonder why?!?
		$missed=rkGetFailedAmount($clusterConnect,$nutanixVMs->data[$i]->name);
		$tmpCount=count($missed->data);
		$myVMs[$totalVM]["missed"]=$tmpCount;
		$myVMs[$totalVM]["sla"]=$nutanixVMs->data[$i]->effectiveSlaDomainName;
		
		$totalVM++;
		print(".");
	}

	print(" Done!\n");

	print("Computing data (one \"dot\" per Hyper-V VM analysed - Total is : ".rkColorOutput(count($hypervVMs->data)).") : ");

	for($i=0;$i<count($hypervVMs->data);$i++)
	{
		$myVMs[$totalVM]["host"]=$hypervVMs->data[$i]->hostId;
		$myVMs[$totalVM]["vmname"]=$hypervVMs->data[$i]->name;

		// In my environment, below call is very slow, I wonder why?!?
		$missed=rkGetFailedAmount($clusterConnect,$hypervVMs->data[$i]->name);
		$tmpCount=count($missed->data);
		$myVMs[$totalVM]["missed"]=$tmpCount;
		$myVMs[$totalVM]["sla"]=$hypervVMs->data[$i]->effectiveSlaDomainName;
		
		$totalVM++;
		print(".");
	}

	print(" Done!\n");
	
	// Sort output per Host 
	sort($myVMs);

	// =========================================================================================================================================
	// Step 2 : Display results with nice padded lines
	// =========================================================================================================================================
	
	print("Displaying results\n\n");
	print("+".str_pad("",105, "-", STR_PAD_RIGHT)."+\n");
	print("| ".str_pad("Object Location",30, " ", STR_PAD_RIGHT)." | ".str_pad("VM",45, " ", STR_PAD_BOTH)." | ".str_pad("SLA (".rkColorRed("missed").")",33, " ", STR_PAD_LEFT)." |\n");
	print("+".str_pad("",105, "-", STR_PAD_RIGHT)."+\n");

	for($i=0;$i<count($myVMs);$i++)
	{
		print("| ".str_pad($myVMs[$i]["host"], 30, " ", STR_PAD_RIGHT));
		print(" | ".str_pad($myVMs[$i]["vmname"], 45, " ", STR_PAD_RIGHT));
		if($myVMs[$i]["missed"]!="0") print(" | ".str_pad($myVMs[$i]["sla"]." (".rkColorRed($myVMs[$i]["missed"]).")", 33, " ", STR_PAD_LEFT)." |\n");
		else print(" | ".str_pad($myVMs[$i]["sla"], 22, " ", STR_PAD_LEFT)." |\n");
	}

	print("+".str_pad("",105, "-", STR_PAD_RIGHT)."+\n");
	print("Total VMs : ".rkColorOutput(count($myVMs))."\n");

	// =========================================================================================================================================
	// Step 3 : Create a CSV for  analysis
	// =========================================================================================================================================

	print("Writing data to file.\n");
	file_put_contents($CSVFileName,"Object Source,VM,SLA,Missed\n");

	for($i=0;$i<count($myVMs);$i++)
	{
		file_put_contents($CSVFileName,$myVMs[$i]["host"].",".$myVMs[$i]["vmname"].",".$myVMs[$i]["sla"].",".$myVMs[$i]["missed"]."\n",FILE_APPEND);
	}
	
	// End the clock
    	$time2=time();
    	$elapsed=$time2-$time1;

	print("Processing time : ".rkColorOutput(date('H:i:s',$elapsed)."\n"));	
	print("\nEnd of script.\n");

?>
