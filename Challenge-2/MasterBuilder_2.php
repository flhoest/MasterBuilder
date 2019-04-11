<?php
	
	/*				__________        ___.            .__  __    
					\______   \ __ __ \_ |__  _______ |__||  | __
					 |       _/|  |  \ | __ \ \_  __ \|  ||  |/ /
					 |    |   \|  |  / | \_\ \ |  | \/|  ||    < 
					 |____|_  /|____/  |___  / |__|   |__||__|_ \
						\/             \/                  \/	
	*/
	
	// Master Challenge #2

	include_once "rkCredentials.php";
	include_once "rkFramework.php";

	$dTZ='Europe/Brussels';
	date_default_timezone_set($dTZ);

	$clusterTZ=json_decode(rkGetClusterDetails($clusterConnect))->timezone->timezone;
	
	// ------------------------------------------------
	// Convert time zones
	// ------------------------------------------------

	function date_convert($dt, $tz1, $df1, $tz2, $df2) 
	{
		  // create DateTime object
		  $d = DateTime::createFromFormat($df1, $dt, new DateTimeZone($tz1));
		  // convert timezone
		  $d->setTimeZone(new DateTimeZone($tz2));
		  // convert dateformat
		  return $d->format($df2);
	}

	// ------------------------------------------------
	// Configuration Section
	// ------------------------------------------------

	// This is the name of the report in the cluster. If the report is temporary a tmp_ prefix will be added for clarity
	$rptName="Master Challenge 2";

	// This is the report speciications - avoid fine tuning this section unless you speak Rubrik reporting fluently ! ;)
	$rptSpecs="
		{
		  \"reportType\": \"Custom\",
		  \"chart1\": {
			\"id\": \"chart1\",
			\"name\": \"Weekly Protection Tasks by SLA Domain\",
			\"chartType\": \"StackedVerticalBar\",
			\"attribute\": \"SlaDomain\",
			\"measure\": \"StackedTotalData\"
		  },
		  \"chart0\": {
			\"id\": \"chart0\",
			\"name\": \"Weekly Protection Tasks by Status\",
			\"chartType\": \"Donut\",
			\"attribute\": \"ObjectName\",
			\"measure\": \"TaskCount\"
		  },
		  \"updateStatus\": \"Ready\",
		  \"name\": \"".$rptName."\",
		  \"filters\": {
			\"dateConfig\": {
			  \"period\": \"PastWeek\"
			},
			\"taskType\": [
			  \"Backup\",
			  \"Replication\",
			  \"Archival\",
			  \"LogArchival\"
			],
			\"taskStatus\": [
			  \"Succeeded\",
			  \"Failed\"
			]
		  },
		  \"reportTemplate\": \"ProtectionTasksSummary\",
		  \"table\": {
			\"columns\": [
			  \"Day\",
			  \"TaskType\",
			  \"ObjectType\",
			  \"Location\",
			  \"SlaDomain\",
			  \"ReplicationSource\",
			  \"ReplicationTarget\",
			  \"ArchivalTarget\",
			  \"AverageDuration\",
			  \"DataStored\",
			  \"SuccessfulTaskCount\",
			  \"FailedTaskCount\",
			  \"CanceledTaskCount\"
			]
		  }
		}";

	// Define when the report has to be sent out to $reportRecipient
	$reportSchedule="0";
	
	// The beow line convert the time to the cluster time to avoid confusion.
	// If you want your cluster at 8pm and your cluster is in another time zone, I'll make sure the right time is set in the cluster,
	// so you have your report at 8pm.
	$clusterTime=date_convert($reportSchedule,$dTZ, 'H', $clusterTZ, 'H');

	// This is the recipient of the report. Enter any valid email address.
	$reportRecipient="flhoest@pccwglobal.com";

	// This is the definition of the schedule, avoid touching this section ...
	$scheduleDefinition="{
			  \"timeAttributes\": {
				\"dailyScheduleHour\": ".$clusterTime.",
				\"weeklyScheduleHour\": 0,
				\"daysOfWeek\": [
				  0
				],
				\"monthlyScheduleHour\": 0,
				\"dayOfMonth\": 0
			  },
			  \"emailAddresses\": [
				\"".$reportRecipient."\"
			  ],
			  \"attachments\": [
				\"Csv\"
			  ]
			}";

	// ------------------------------------------------
	// Entry point
	// ------------------------------------------------

	// Step 1 : Create the report based on specifications

	system("clear");
	print("---------------\n");
	print("Rubrik Reporting module.\n");
	print("----------------------------------\n");
	print("Creating report...\n");
	$err_code=rkCreateReport($clusterConnect,$rtpName,$rptSpecs);
	if($err_code!=201)
	{
		print(rkColorRed("Something went wrong when creating report")."\n");
		print("Report with name ".rkColorOutput($rptName)." already exists ?\nExiting\n");
		exit();
	}
	print("Done!.\n");
	
	// Step 2 : attach email recipient and create a shcedule

	$rptID=rkGetReportID($clusterConnect,$rptName);

	print("Adding Schedule to it...\n");	
	
	$err_code=rkCreateReportSchedule($clusterConnect,$rptID,$scheduleDefinition);
	if($err_code!=200)
	{
		print(rkColorRed("Something went wrong when applying schedule. Exiting")."\n");
		exit();
	}
	print("Done!\n");
	
	// Step 3 : Refresh the data attached to the report

	print("Refresh data from report...\n");
	$err_code=rkRefreshReport($clusterConnect,$rptID);
	if($err_code!=202)
	{
		print(rkColorRed("Something went wrong when refreshing report. Exiting")."\n");
		exit();
	}
	print("Done!\n");

	print("------------------------------------------------------------\n\n");
	print("Watch out the ".rkColorOutput($reportRecipient)." mailbox for new mail containing report data at around ".rkColorOutput($reportSchedule.":00:00")."\n\n");
	
	print("Please be aware that a report called ".rkColorOutput($rptName)." has been added into your cluster ".rkColorOutput($clusterConnect["ip"])."\n");
	print("Feel free to delete it if this is not needed!.\n");
	print("\nEnd of script.\n");

?>
