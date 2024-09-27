<?php
echo'

<li '.$Awardsclsactive.'><a href="Awards.php"><i class="fas fa-book-reader"></i> <span> Awards List </span></a></li>
<li '.$Resultsclsactive.'><a href="Results.php"><i class="fas fa-book-reader"></i> <span> Results List </span></a></li>

<li class="submenu">
<a href="#" '.$teshdashclsactive.'><i class="fas fa-chalkboard-teacher"></i> <span> Teachers </span> <span class="menu-arrow"></span></a>
	<ul '.$teshdashvis.'>
		<li '.$teshdashdashboardactive.'><a href="Teachers_Dashboard.php">Teacher Dashboard</a></li>
		<li '.$Teachersdashboardactive.'><a href="Teachers.php">Teacher List</a></li>
		<li '.$Departmentsdashboardactive.'><a href="Departments.php">Departments View</a></li>
	</ul>
</li>


<li class="submenu">
	<a href="#" '.$stdlistclsactive.'><i class="fas fa-chalkboard-teacher"></i> <span> Students </span> <span class="menu-arrow"></span></a>
	<ul '.$stdlistis.'>
		<li '.$stddashboardactive.'><a href="Student_Dashboard.php"> Students Dashboard </a></li>
		<li '.$stdlistactive.'><a href="Students.php"> Students </a></li>
		<li '.$Classesdashboardactive.'><a href="Classes.php"> Classes </a></li>
		<li '.$Assignmentdashboardactive.'><a href="Assignment.php"> Assignment </a></li>
		<li '.$Subjectsdashboardactive.'><a href="Subjects.php"> Subjects </a></li>
		<li '.$Achievementsdashboardactive.'><a href="Achievements.php"> Achievements </a></li>
		<li '.$Alumnisdashboardactive.'><a href="Alumnis.php"> Alumnis </a></li>
		<li '.$Academicsdashboardactive.'><a href="Academics.php"> Academics </a></li>
		<li '.$Diarysdashboardactive.'><a href="Diarys.php"> Diarys </a></li>
	</ul>
</li>



<li class="menu-title">
	<span>Management</span>
</li>
<li class="submenu">
	<a href="#" '.$Banksclsactive.'><i class="fas fa-file-invoice-dollar"></i> <span> Accounts</span> <span class="menu-arrow"></span></a>
	<ul '.$Banksvis.'>
		<li '.$Banksdashboardactive.'><a href="Banks.php"> Banks </a></li>
		<li '.$FeesCollectionsdashboardactive.'><a href="FeesCollections.php">Fees Collection</a></li>
		<li '.$FeesTypedashboardactive.'><a href="FeesType.php"> Fees Type </a></li>
		<li '.$Expensesdashboardactive.'><a href="Expenses.php">Expenses</a></li>
		<li '.$Salarydashboardactive.'><a href="Salary.php">Salary</a></li>
	</ul>
</li>

<li class="submenu">
	<a href="#" '.$Libraryclsactive.'><i class="fas fa-chalkboard-teacher"></i> <span> Library </span> <span class="menu-arrow"></span></a>
	<ul '.$vLibraryvis.'>
		<li '.$Librarydashboardactive.'><a href="Library.php"> Library </a></li>
		<li '.$Authorsdashboardactive.'><a href="Authors.php"> Authors </a></li>
		<li '.$Languagedashboardactive.'><a href="Language.php"> Language </a></li>
		<li '.$Publisherdashboardactive.'><a href="Publisher.php"> Publisher </a></li>
		<li '.$BookTypedashboardactive.'><a href="BookType.php"> Book Type </a></li>
	</ul>
</li>

<li class="submenu">
	<a href="#" '.$Hostelclsactive.'><i class="fas fa-hotel"></i> <span> Hostel </span> <span class="menu-arrow"></span></a>
	<ul '.$vLibraryvis.'>
		<li '.$Languagedashboardactive.'><a href="Hostels.php"> Hostels </a></li>
		<li '.$Librarydashboardactive.'><a href="Rooms.php"> Rooms </a></li>
		<li '.$Authorsdashboardactive.'><a href="Furniture.php"> Furniture </a></li>
		<li '.$Publisherdashboardactive.'><a href="Mess.php"> Mess </a></li>
		<li '.$BookTypedashboardactive.'><a href="Employees.php"> Employees </a></li>
	</ul>
</li>




<li '.$Holidaysclsactive.'><a href="Holidays.php"><i class="fas fa-holly-berry"></i> <span>Holidays</span></a></li>
<li '.$Holidaysclsactive.'><a href="HolidaysTypes.php"><i class="fas fa-holly-berry"></i> <span>Holiday Types</span></a></li> 
<li '.$Eventsclsactive.'><a href="Fees.php"><i class="fas fa-comment-dollar"></i> <span>Fees</span></a></li>
<li '.$Examsclsactive.'><a href="Exams.php"><i class="fas fa-clipboard-list"></i> <span>Exam list</span></a></li>
<li '.$Eventsclsactive.'><a href="Events.php"><i class="fas fa-calendar-day"></i> <span>Events</span></a></li>
<li '.$TimeTableclsactive.'><a href="TimeTable.php"><i class="fas fa-table"></i> <span>Time Table</span></a></li>


<li class="menu-title">
<span>Pages</span>
</li>

<li '.$Holidaysclsactive.'><a href="Education.php"><i class="fas fa-user-graduate"></i> <span>Education</span></a></li>
<li '.$Holidaysclsactive.'><a href="Designation.php"><i class="fas fa-user-tie"></i> <span>Designations</span></a></li> 
<li '.$Holidaysclsactive.'><a href="Education_Session.php"><i class="fas fa-building"></i> <span>Education Session</span></a></li> 

<li class="menu-title">
<span>Others</span>
</li>
<li '.$Sportsclsactive.'><a href="Sports.php"><i class="fas fa-baseball-ball"></i> <span>Sports</span></a></li>
<li '.$Hostelclsactive.'><a href="Hostel.php"><i class="fas fa-hotel"></i> <span>Hostel</span></a></li>
<li '.$Transportclsactive.'><a href="Transport.php"><i class="fas fa-bus"></i> <span>Transport</span></a></li>
<li '.$Adminsclsactive.'><a href="Admins.php"><i class="fas fa-bus"></i> <span> Admins </span></a></li>
'; ?>